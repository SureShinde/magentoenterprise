<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Snippets
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Snippets_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getProduct() 
	{
        $product = Mage::registry('current_product');
        return ($product && $product->getEntityId()) ? $product : false;
    }

	public function getCategory() 
	{
        $category = Mage::registry('current_category');
        return ($category && $category->getEntityId() && !Mage::registry('current_product')) ? $category : false;
    }

	public function getSnippetsEnabled($type = 'product') 
	{
		$extension = Mage::getStoreConfig('snippets/general/enabled');		
		if($type == 'product') {
			$enabled = Mage::getStoreConfig('snippets/products/enabled');
		} else {
			$enabled = Mage::getStoreConfig('snippets/category/enabled');		
		}		
		if($extension && $enabled) {	
			return true;
		} else {
			return false;
		}	
	}

    public function getProductSnippets($product = null, $simple = null, $simple_price = null) 
    {
		if(empty($product)) {
			$product = $this->getProduct();
			$simple = $product;
		}

		if(!empty($product)) {		
			$snippets = array();
			$snippets['name'] = $product->getName();			
			if($description = $this->getProductDescription($product)) {
				$snippets['description'] = $description;
			}
			if($thumbnail = $this->getProductThumbnail($product)) {
				$snippets['thumbnail'] = $thumbnail;
			}	
			if($image = $this->getProductImage($product, $simple)) {
				$snippets['image'] = $image;
			}				
			$snippets['offers'] = $this->getProductOffers($product, $simple_price);
			$snippets['availability'] = $this->getAvailability($product, $simple);
			$snippets['condition'] = $this->getCondition($product, $simple);
			$snippets['rating'] = $this->getProductRatings($product);
			$snippets['extra'] = $this->getProductExtraFields($product, $simple);
			if(($snippets['offers']['price_only'] < 0.01) && (Mage::getStoreConfig('snippets/products/hide_noprice'))) {		
				return false;
			} else {				
				return $snippets; 
			}	
		} 		
    }

    public function getJsonProductSnippets() 
    {	
		$product = $this->getProduct();
		$snippets_type = Mage::getStoreConfig('snippets/products/type');
		$snippets = array();
		if(($snippets_type == 'json') && (!empty($product))) {	
	    	if($product->getTypeId() == 'configurable') {
				$simples = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
				$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
				$type_prices = array();
				$att_prices = array();
				$base_price = $product->getFinalPrice();
				foreach ($attributes as $attribute){
					$prices = $attribute->getPrices();
					foreach ($prices as $price){
						if ($price['is_percent']) { 
							$att_prices[$price['value_index']] = (float)$price['pricing_value'] * $base_price / 100;
						} else {
							$att_prices[$price['value_index']] = (float)$price['pricing_value'];
						}
					}
				}
				$simple = $product->getTypeInstance()->getUsedProducts();
				$simple_prices = array();	
				foreach($simple as $sProduct) {
					$total_price = $base_price;
					foreach($attributes as $attribute) {
						$value = $sProduct->getData($attribute->getProductAttribute()->getAttributeCode());
						if(isset($att_prices[$value])) {
							$total_price += $att_prices[$value];
						}
					}
					$taxHelper = Mage::helper('tax');
					$type_prices[$sProduct->getEntityId()] = number_format($taxHelper->getPrice($product, $total_price, true), 2);
				}
				foreach($simples as $simple) {
					if(!empty($type_prices[$simple->getEntityId()])) {
						$simple = Mage::getModel('catalog/product')->load($simple->getEntityId());
						$snippets_data = $this->getProductSnippets($product, $simple, $type_prices[$simple->getEntityId()]);
						$snippets[] = $this->getJsonProductSnippetsData($snippets_data);			
					}	
				}
	    	} else {
				$snippets_data = $this->getProductSnippets();
		    	$snippets[] = $this->getJsonProductSnippetsData($snippets_data);
		    }
			return $snippets;				
	    }	
    }
    
    
    public function getJsonProductSnippetsData($snippets_data) 
    {			
		$snippets = array();
		$snippets['@context'] = 'http://schema.org';
		$snippets['@type'] = 'Product';
		$snippets['name'] = $snippets_data['name'];
		if(!empty($snippets_data['description'])) {
			$snippets['description'] = $snippets_data['description'];
		}
		if(!empty($snippets_data['image'])) {
			$snippets['image'] = $snippets_data['image'];
		}
		$snippets['offers'] = '';				
		$snippets['offers']['@type'] = $snippets_data['offers']['type'];

		if(isset($snippets_data['availability'])) {
			$snippets['offers']['availability'] = $snippets_data['availability']['url'];
		}
		if(isset($snippets_data['offers']['price_low'])) {
			$snippets['offers']['lowprice'] = $snippets_data['offers']['clean_low'];
		} else {
			$snippets['offers']['price'] = $snippets_data['offers']['clean'];
		}
		$snippets['offers']['priceCurrency'] = $snippets_data['offers']['currency'];
		if(isset($snippets_data['condition'])) {
			if(isset($snippets_data['condition']['url'])) {
				$snippets['offers']['itemCondition'] = $snippets_data['condition']['url'];						
			}	
		}
		if(!empty($snippets_data['offers']['seller'])) {
			$snippets['offers']['seller']['@type'] = 'Organization';
			$snippets['offers']['seller']['name'] = $snippets_data['offers']['seller'];
		}				
		if(isset($snippets_data['offers']['extra_offer'])) {
		  $offers = array();
		  $offers[] = $snippets_data['offers'];
		  foreach($snippets_data['offers']['extra_offer'] as $extra_offer) {
			  if($extra_offer['currency'] != $snippets['offers']['priceCurrency']) {
				  $offers_extra['@type'] =  $snippets_data['offers']['type'];
				  $offers_extra['availability'] = $snippets_data['availability']['url'];
				  $offers_extra['price'] = $extra_offer['price'];
				  $offers_extra['priceCurrency'] = $extra_offer['currency'];
				  $offers[] = $offers_extra;				
			  }
		  }
		  $snippets['offers'] = $offers;
		}
		if((isset($snippets_data['rating']['count'])) && ($snippets_data['rating']['percentage'] > 0)) {
			$snippets['aggregateRating'] = '';
			$snippets['aggregateRating']['@type'] = 'AggregateRating';
			$snippets['aggregateRating']['ratingValue'] = $snippets_data['rating']['avg'];
			$snippets['aggregateRating']['bestRating'] = $snippets_data['rating']['best'];
			$snippets['aggregateRating'][$snippets_data['rating']['type']] = $snippets_data['rating']['count'];
		}	
		if($extrafields = $snippets_data['extra']) {
			foreach($extrafields as $field) { 
				$snippets[$field['itemprop']] = $field['clean'];					
			}
		}
		return $snippets;				
	}
	
    public function getCategorySnippets() 
    {
		if($category = $this->getCategory()) {					
			if(Mage::getStoreConfig('snippets/category/categories_filter')) {
				$cats = Mage::getStoreConfig('snippets/category/categories');
				if(!empty($cats)) {
					$cats = explode(',', $cats);
					if(!in_array($category->getId(), $cats)) {
						return false;
					}
				} else {
					return false;
				}
			}
			$snippets = array();
			$snippets['name'] = $category->getName();
			if($description = $this->getCategoryDescription($category)) {
				$snippets['description'] = $description;
			}				
			if($thumbnail = $this->getCategoryThumbnail($category)) {
				$snippets['thumbnail'] = $thumbnail;
			}		
			$snippets['offers'] = $this->getCategoryOffers($category);
			$snippets['availability']['url'] = 'http://schema.org/InStock';
			$snippets['availability']['text'] = Mage::helper('snippets')->__('In stock');
			$snippets['rating'] = $this->getCategoryRatings($category);			

			if(($snippets['offers']['clean_low'] < 0.01) && (Mage::getStoreConfig('snippets/category/noprice'))) {		
				return false;
			} else {
				return $snippets; 			
			}
		} 		
		return false;
    }

    public function getJsonCategorySnippets() 
    {			
		$snippets_data = $this->getCategorySnippets();
		$snippets_type = Mage::getStoreConfig('snippets/category/type');
		$category = $this->getCategory();
		if(!empty($snippets_data) && ($snippets_type == 'json') && (!empty($category))) {	
			$snippets['@context'] = 'http://schema.org';
			$snippets['@type'] = 'Product';
			$snippets['name'] = $snippets_data['name'];				
			if(isset($snippets_data['description'])) {
				$snippets['description'] = $snippets_data['description'];
			}	
			if(isset($snippets_data['thumbnail'])) {
				$snippets['image'] = $snippets_data['thumbnail'];
			}	
			if(isset($snippets_data['offers']['price_low'])) {
				$snippets['offers'] = '';
				$snippets['offers']['@type'] = 'AggregateOffer';
				if(isset($snippets_data['availability'])) {
					$snippets['offers']['availability'] = 'http://schema.org/InStock';
				}
				if(isset($snippets_data['offers']['price_high'])) {
					$snippets['offers']['lowprice'] = $snippets_data['offers']['clean_low'];
					$snippets['offers']['highprice'] = $snippets_data['offers']['clean_high'];						
				} else {
					$snippets['offers']['lowprice'] = $snippets_data['offers']['clean_low'];
				}
				$snippets['offers']['priceCurrency'] = $snippets_data['offers']['currency'];
				if(!empty($snippets_data['offers']['seller'])) {
					$snippets['offers']['seller']['@type'] = 'Organization';
					$snippets['offers']['seller']['name'] = $snippets_data['offers']['seller'];
				}	
			}	
			if((isset($snippets_data['rating']['count'])) && ($snippets_data['rating']['percentage'] > 0)) {
				$snippets['aggregateRating'] = '';
				$snippets['aggregateRating']['@type'] = 'AggregateRating';
				$snippets['aggregateRating']['ratingValue'] = $snippets_data['rating']['avg'];
				$snippets['aggregateRating']['bestRating'] = $snippets_data['rating']['best'];
				$snippets['aggregateRating'][$snippets_data['rating']['type']] = $snippets_data['rating']['count'];
			}	
			if(($snippets_data['offers']['qty'] < 1) && (Mage::getStoreConfig('snippets/category/noprice'))) {		
				return false;
			} else {
				return $snippets;
			}
		} 
    }

    public function getLocalBusinessSnippets() 
    {			
		$snippets = array();
		if(Mage::getStoreConfig('snippets/system/localbusiness')) {		
			$snippets['@context'] = 'http://schema.org';
			$snippets['@type'] = Mage::getStoreConfig('snippets/system/seller_type');			
			$snippets['@id'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);		
			if($name = Mage::getStoreConfig('snippets/system/name')) {
				$snippets['name'] = $name;
			}
			if($telephone = Mage::getStoreConfig('snippets/system/telephone')) {
				$snippets['telephone'] = $telephone;
			}			
			if($logo_url = Mage::getStoreConfig('snippets/system/logo_url')) {
				$snippets['image'] = $logo_url;
			}				
			if($street = Mage::getStoreConfig('snippets/system/street')) {
				$snippets['address']['@type'] = 'PostalAddress';
				$snippets['address']['streetAddress'] = $street;

				if($locality = Mage::getStoreConfig('snippets/system/locality')) {
					$snippets['address']['addressLocality'] = $locality;
				}					
				if($region = Mage::getStoreConfig('snippets/system/region')) {
					$snippets['address']['addressRegion'] = $region;
				}					
				if($postalcode = Mage::getStoreConfig('snippets/system/postalcode')) {
					$snippets['address']['postalCode'] = $postalcode;
				}					
				if($country = Mage::getStoreConfig('snippets/system/country')) {
					$snippets['address']['addressCountry'] = $country;
				}

				$latitude = Mage::getStoreConfig('snippets/system/latitude');
				$longitude = Mage::getStoreConfig('snippets/system/longitude');
				if(!empty($latitude) && !empty($longitude)) {
					$snippets['geo']['@type'] = 'GeoCoordinates';
					$snippets['geo']['latitude'] = $latitude;
					$snippets['geo']['longitude'] = $longitude;
				}
			}				
			$openinghours = @unserialize(Mage::getStoreConfig('snippets/system/openinghours'));
			if(!empty($openinghours)) {
				foreach($openinghours as $open) {
					$opening_array = array();	
					$opening_array['@type'] = 'OpeningHoursSpecification';
					$opening_array['dayOfWeek'] = $open['day'];				
					$opening_array['opens'] = $open['from'];				
					$opening_array['closes'] = $open['to'];				
					$snippets['openingHoursSpecification'][] = $opening_array;
					unset($opening_array);
				}				
			}
			if(!empty($snippets)) {		
				if(Mage::getStoreConfig('snippets/system/ratings')) {
					if(Mage::getStoreConfig('snippets/system/rating_schema') == 'LocalBusiness') {
						if($reviews = $this->getAggregateRating()) {
							if(Mage::getStoreConfig('snippets/system/index_only')) {
								if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
									$snippets['aggregateRating'] = $reviews;	
								}
							} else {
								$snippets['aggregateRating'] = $reviews;	
							}	
						}	
					}
				}
				return $snippets;
			}	
		}		
	}	

    public function getOrganizationSnippets() 
    {			
		$snippets = array();
		if(Mage::getStoreConfig('snippets/system/organization')) {		
			$snippets['@context'] = 'http://schema.org';
			$snippets['@type'] = 'Organization';
			$snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);			
			if($logo_url = Mage::getStoreConfig('snippets/system/logo_url')) {
				$snippets['logo'] = $logo_url; 				
			}			
			$contacts = @unserialize(Mage::getStoreConfig('snippets/system/contacts'));
			if(!empty($contacts)) {
				$cont_array = array();
				foreach($contacts as $contact) {
					if(!empty($contact['telephone'])) {
						$cont_array['@type'] = 'ContactPoint';
						$cont_array['telephone'] = $contact['telephone'];
						if(!empty($contact['contacttype'])) {
							$cont_array['contactType'] = $contact['contacttype'];
						}	
						if(!empty($contact['contactoption'])) {
							if(strpos($contact['contactoption'], ',') !== false) {
								$cont_array['contactOption'] = explode(',', $contact['contactoption']);
							} else {									
								$cont_array['contactOption'] = $contact['contactoption'];
							}	
						}
						if(!empty($contact['area'])) {
							if(strpos($contact['area'], ',') !== false) {
								$cont_array['areaServed'] = explode(',', $contact['area']);
							} else {
								$cont_array['areaServed'] = $contact['area'];
							}	
						}	
						if(!empty($contact['languages'])) {
							if(strpos($contact['languages'], ',') !== false) {
								$cont_array['availableLanguage'] = explode(',', $contact['languages']);
							} else {
								$cont_array['availableLanguage'] = $contact['languages'];
							}	
						}																			
					}
					if(!empty($cont_array)) {
						$snippets['contactPoint'][] = $cont_array;
					}					
				}
			}
			$social_urls = @unserialize(Mage::getStoreConfig('snippets/system/social'));
			if(!empty($social_urls)) {
				foreach($social_urls as $social) {			
					if(!empty($social['url'])) {
						$snippets['sameAs'][] = $social['url'];
					}
				}
			}
			if(!empty($snippets)) {		
				if(Mage::getStoreConfig('snippets/system/ratings')) {
					if(Mage::getStoreConfig('snippets/system/rating_schema') == 'Organization') {
						if($reviews = $this->getAggregateRating()) {
							if(Mage::getStoreConfig('snippets/system/index_only')) {
								if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
									$snippets['aggregateRating'] = $reviews;	
								}
							} else {
								$snippets['aggregateRating'] = $reviews;	
							}	
						}	
					}
				}
				return $snippets;
			}	
		} 		
		return false;
    }    

    public function getWebsiteSnippets() 
    {			
		$search = array();
		if(Mage::getStoreConfig('snippets/system/sitelinkssearch')) {
			if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
				$search['@type'] = 'SearchAction';
				$search['target'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'catalogsearch/result/?q={search_term_string}';
				$search['query-input'] = 'required name=search_term_string';
			}
		}
		$sitename = Mage::getStoreConfig('snippets/system/sitename');
  		if(!empty($search) || !empty($sitename)) {
			$sitename_name = Mage::getStoreConfig('snippets/system/sitename_name');
			$sitename_alternate = Mage::getStoreConfig('snippets/system/sitename_alternate');
			$snippets = array();
	  		$snippets['@context'] = 'http://schema.org';
	  		$snippets['@type'] = 'WebSite';
	  		$snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			if(!empty($sitename) && !empty($sitename_name)) {
		  		$snippets['name'] = $sitename_name;
			}
			if(!empty($sitename) && !empty($sitename_alternate)) {
		  		$snippets['alternateName'] = $sitename_alternate;
			}
			if(!empty($search)) {
				$snippets['potentialAction'] = $search;
			}	
	  		return $snippets;	  		
  		}
	}

    public function getWebPageSnippets() 
    {			
		$snippets = array();
		if(Mage::getStoreConfig('snippets/system/ratings')) {
			if(Mage::getStoreConfig('snippets/system/rating_schema') == 'WebPage') {
				if($reviews = $this->getAggregateRating()) {
					if(Mage::getStoreConfig('snippets/system/index_only')) {
						if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
					  		$snippets['@context'] = 'http://schema.org';
					  		$snippets['@type'] = 'WebPage';
							$snippets['aggregateRating'] = $reviews;	
						}
					} else {
				  		$snippets['@context'] = 'http://schema.org';
				  		$snippets['@type'] = 'WebPage';
						$snippets['aggregateRating'] = $reviews;	
					}	
				}	
			}
		}
		if(!empty($snippets)) {
			return $snippets;
		}	
	}

    public function getJsonBlogSnippets() 
    {			
		$snippets = array();
		$enable = Mage::getStoreConfig('snippets/blog/enable');
		if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'blog_post_view') {	
			if($blog = Mage::getSingleton('blog/post')) {
				$post = Mage::getModel('blog/post')->load($blog->getId());
				$title = $post->getTitle();
				$user = $post->getUser();
				$short_content = $post->getShortContent();
				$post_content = $post->getPostContent();
				$created_time = $post->getCreatedTime();
				$update_time = $post->getUpdateTime();
				if(empty($update_time)) {
					$update_time = $created_time;
				}
				$image_width = $image_height = $image_url = '';
	            $processor = Mage::getModel('core/email_template_filter');
				$post_content = $processor->filter($post_content);				
			    preg_match_all('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $post_content, $image);
				if(!empty($image['src'])) {
					foreach($image['src'] as $img) {
						if(!preg_match('(http|//)', $img)) { 
							$img_loc = Mage::getBaseDir('base') . $img;
							if(file_exists($img_loc)) {
								list($width, $height) = getimagesize($img_loc);
								$img = Mage::getBaseUrl() . $img;
							} else {
								$width = $height = 0;
							}
						} else {
							list($width, $height) = getimagesize($img);
						}
						if($width > 0 && ($width > $image_width)) {
							$image_url = $img;
							$image_width = $width;
							$image_height = $height;							
							if($image_width > 695) {
								break;
							}
						}	
					}
				}
				if(($enable > 1) && (empty($image_url))) {
					return;
				}
				if($enable == 3) {
					if($image_width < 695) {
						return;
					}	
				}
				$snippets['@context'] = 'http://schema.org';
				$snippets['@type'] = 'BlogPosting';
				$snippets['mainEntityOfPage']['@type'] = 'WebPage';
				$snippets['mainEntityOfPage']['@id'] = $post->getAddress();
				$snippets['headline'] = trim(strip_tags($title));
				if(!empty($image_url)) {
					$snippets['image']['@type'] = 'ImageObject';
					$snippets['image']['url'] = $image_url;
					$snippets['image']['height'] = $image_height;
					$snippets['image']['width'] = $image_width;
				}
				$snippets['datePublished'] = $created_time;
				$snippets['dateModified'] = $update_time;
				$snippets['author']['@type'] = 'Person';
				$snippets['author']['name'] = $user;
				$snippets['publisher']['@type'] = 'Organization';
				$snippets['publisher']['name'] = Mage::getStoreConfig('snippets/system/sitename_name'); 
				if($logo = Mage::getStoreConfig('snippets/blog/logo_url')) {
					list($logo_width, $logo_height) = getimagesize($logo);
					$snippets['publisher']['logo']['@type'] = 'ImageObject';
					$snippets['publisher']['logo']['url'] = Mage::getStoreConfig('snippets/blog/logo_url');
					$snippets['publisher']['logo']['width'] = $logo_width;
					$snippets['publisher']['logo']['height'] = $logo_height;
				}
				$snippets['description'] = trim(strip_tags($short_content));
			}
		}
		if(!empty($snippets)) {
			return $snippets;
		}			
	}
		
    public function getAggregateRating() 
    {					
		$rating_value = ''; $rating_count = ''; $rating_best = '';
		$type = Mage::getStoreConfig('snippets/system/rating_source'); 		
		if($type == 'shopreview') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Shopreview')) { 
				$total = Mage::helper('shopreview')->getTotalScore();
				if(isset($total['total'])) {
					$rating_value = $total['total'];		
					$rating_count = Mage::helper('shopreview')->getReviewCount();
					$rating_best = '100';
				}
			}
		}
		if($type == 'feedbackcompany') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Feedbackcompany')) { 
				$total = Mage::helper('feedbackcompany')->getTotalScore();				
				if(isset($total['percentage'])) {
					$rating_value = $total['percentage'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}
		if($type == 'webwinkelkeur') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Webwinkelconnect')) { 
				$total = Mage::helper('webwinkelconnect')->getTotalScore();				
				if(isset($total['average'])) {
					$rating_value = $total['average'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}
		if($type == 'trustpilot') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Trustpilot')) { 
				$total = Mage::helper('trustpilot')->getTotalScore();				
				if(isset($total['score'])) {
					$rating_value = $total['score'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}
		if($type == 'kiyoh') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Kiyoh')) { 
				$total = Mage::helper('kiyoh')->getTotalScore();				
				if(isset($total['score'])) {
					$rating_value = $total['score'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}
		if(($rating_value > 0) && ($rating_count > 0)) {		
			$rating = array();
			$rating['@type'] = 'AggregateRating';
			$rating['ratingValue'] = $rating_value;		
			$rating['reviewCount'] = $rating_count; 
			$rating['bestRating'] = $rating_best;
			return $rating;
		}			
	}
	
	public function getJsonBreadcrumbs($breadcrumbs) 
	{		
		if($breadcrumbs) {
			$cacheKeyInfo = $breadcrumbs->getCacheKeyInfo();
			if(!empty($cacheKeyInfo['crumbs'])) {
				$crumbs = unserialize(base64_decode($cacheKeyInfo['crumbs']));
				$listitems = array(); $i = 1;
				if($crumbs) {
					$snippets['@context'] = 'http://schema.org';
					$snippets['@type'] = 'BreadcrumbList';
					foreach($crumbs as $crumb) {
						if($crumb['link']) {
							$list['@type'] = 'ListItem';
							$list['position'] = $i;
							$list['item']['@id'] = $crumb['link'];
							if($i == 1) {
								$list['item']['name'] = $this->getFirstBreadcrumbTitle($crumb['label']);
							} else {
								$list['item']['name'] = $crumb['label'];
							}	
							$listitems[] = $list;
							$i++;
						}
					}
					$snippets['itemListElement'] = $listitems;							
					return $snippets;		
				}
			}
		}
	}

    public function getProductMetatags() 
    {
		if($product = $this->getProduct()) {			
			$meta = array();			
			$pin_enabled = Mage::getStoreConfig('snippets/metadata/product_pinterest');
			$twitter_user = Mage::getStoreConfig('snippets/metadata/twitter_name');
			$twitter_enabled = Mage::getStoreConfig('snippets/metadata/product_twitter');
			$product_markup = Mage::getStoreConfig('snippets/products/type');
			$product_enabled = Mage::getStoreConfig('snippets/products/enabled');
			$offers = ''; $availability = '';
			if(($twitter_user && $twitter_enabled) || ($pin_enabled)) {			
				$offers = $this->getProductOffers($product);
				$availability = $this->getAvailability($product);
			}
			if($pin_enabled) {
				if((!$product_enabled) || ($product_markup == 'json')) {
					$meta['og:type'] = 'product';
					$meta['og:title'] = htmlspecialchars($product->getName());
					if(isset($offers['clean'])) {
						$meta['product:price:amount'] = $offers['clean'];
						$meta['product:price:currency'] = $offers['currency'];				
					}	
					if(Mage::getStoreConfig('snippets/system/sitename_name')) {
						$meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
					}	
					$meta['og:url'] = $this->getCurrentUrl();
					$description = str_replace(array("\r\n", "\r", "\n"), '', $this->getProductDescription($product));
					$meta['og:description'] = htmlspecialchars($description);
					$meta['og:image'] = $this->getProductImage($product);
					if(isset($availability['url'])) {
						if($availability['url'] == 'http://schema.org/InStock') {
							$meta['og:availability'] = 'instock';					
						} else {
							$meta['og:availability'] = 'out of stock';											
						}	
					}
				}
			}
			if($twitter_user && $twitter_enabled) {			
				$prices = $offers;
				if($twitter_user[0] != '@') { $twitter_user = '@'.$twitter_user; }
				$meta['twitter:card'] = 'summary';
				$meta['twitter:site'] = $twitter_user;			
				$meta['twitter:title'] = htmlspecialchars($product->getName());
				if($description = $this->getProductDescription($product)) {
					$description = str_replace(array("\r\n", "\r", "\n"), '', $description);
					$meta['twitter:description'] = htmlspecialchars($description);
				} else {
					$meta['twitter:description'] = htmlspecialchars($product->getName()) . ' - ' . $prices['price'];
				}	
				$meta['twitter:image'] = $this->getProductImage($product);
			}							
			return $meta;
		}
		return false;
	}

    public function getCategoryMetatags() 
    {
		if($category = $this->getCategory()) {			
			$meta = array();			
			$pin_enabled = Mage::getStoreConfig('snippets/metadata/category_pinterest');
			$twitter_user = Mage::getStoreConfig('snippets/metadata/twitter_name');
			$twitter_enabled = Mage::getStoreConfig('snippets/metadata/category_twitter');
			$category_markup = Mage::getStoreConfig('snippets/category/type');
			$category_enabled = Mage::getStoreConfig('snippets/category/enabled');			
			$offers = '';
			if(($twitter_user && $twitter_enabled) || ($pin_enabled)) {			
				$offers = $this->getCategoryOffers($category);
			}		
			if($pin_enabled) {
				if((!$category_enabled) || ($category_markup == 'json')) {
					if(isset($offers['clean_low'])) {
						$meta['og:type'] = 'product';
						$meta['og:title'] = htmlspecialchars($category->getName());
						$meta['product:price:amount'] = $offers['clean_low'];
						$meta['product:price:currency'] = $offers['currency'];				
						if(Mage::getStoreConfig('snippets/system/sitename_name')) {
							$meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
						}	
						$meta['og:url'] = $this->getCurrentUrl();
						$description = str_replace(array("\r\n", "\r", "\n"), '', $this->getCategoryDescription($category));
						$meta['og:description'] = htmlspecialchars($description);
						if($image = $this->getCategoryImage($category)) {
							$meta['og:image'] = $this->getCategoryImage($category);
						}
						$meta['og:availability'] = 'instock';					
					}
				}
			}			
			if($twitter_user && $twitter_enabled) {			
				$prices = $offers;
				if($twitter_user[0] != '@') { $twitter_user = '@'.$twitter_user; }
				$meta['twitter:card'] = 'summary';
				$meta['twitter:site'] = $twitter_user;			
				$meta['twitter:title'] = htmlspecialchars($category->getName());
				if($description = $this->getProductDescription($category)) {
					$description = str_replace(array("\r\n", "\r", "\n"), '', $description);
					$meta['twitter:description'] = htmlspecialchars($description);
				} else {
					if(!empty($prices['price_low'])) {
						$meta['twitter:description'] = htmlspecialchars($category->getName()) . ' - ' . $prices['price_low'];
					}	
				}	
				$meta['twitter:image'] = $this->getCategoryImage($category);
			}	
			return $meta;
		}
		return false;
	}

    public function getCmsMetatags() 
    {
		$cms_metadata = Mage::getStoreConfig('snippets/metadata/cms_metadata');
		$twitter = Mage::getStoreConfig('snippets/metadata/cms_twitter');
		if($cms_metadata || $twitter) {
			$meta = array();			
			$cms_title = Mage::getStoreConfig('snippets/metadata/cms_title');
			$cms_description = Mage::getStoreConfig('snippets/metadata/cms_description');
			$cms_logo = Mage::getStoreConfig('snippets/metadata/cms_logo');
			$logo_url = Mage::getStoreConfig('snippets/system/logo_url');
			$twitter_username = Mage::getStoreConfig('snippets/metadata/twitter_name');
			if($cms_metadata && ($cms_title || $cms_description || $cms_logo)) {			
				if($cms_title) {
					$meta['og:title'] = htmlspecialchars(Mage::getSingleton('cms/page')->getTitle());
				}	
				if($cms_description) {
					$meta['og:description'] = htmlspecialchars(Mage::getSingleton('cms/page')->getDescription());
				}
				if($cms_logo && $logo_url) {
					$meta['og:image'] = $logo_url;
				}
				if(Mage::getSingleton('cms/page')->getIdentifier() == 'home') {
					$meta['og:type'] = 'website';
				} else {
					$meta['og:type'] = 'article';
				}	
				$meta['og:url'] = $this->getCurrentUrl();
			}
			if($twitter && $twitter_username) {
				$meta['twitter:card'] = 'summary';
				$meta['twitter:site'] = '@' . $twitter_username;
				$meta['twitter:title'] = htmlspecialchars(Mage::getSingleton('cms/page')->getTitle());
				$meta['twitter:description'] = htmlspecialchars(Mage::getSingleton('cms/page')->getDescription());
				if($logo_url) {
					$meta['twitter:image'] = $logo_url;
				}
			}		
			return $meta;
		}
	}
			
	public function getAvailability($product, $simple = null) 
	{
		$show_stock = Mage::getStoreConfig('snippets/products/stock'); 
		if($show_stock) {
			$availability = array();
			if(!empty($simple)) {
				$availability['url'] = ($simple->isAvailable() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock');		
				$availability['text'] = ($simple->isAvailable() ? Mage::helper('snippets')->__('In stock') : Mage::helper('snippets')->__('Out of Stock'));
			} else {
				$availability['url'] = ($product->isAvailable() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock');		
				$availability['text'] = ($product->isAvailable() ? Mage::helper('snippets')->__('In stock') : Mage::helper('snippets')->__('Out of Stock'));			
			}
			return $availability;		
		}
		return false;		
	}

	public function getCondition($product, $simple = null) 
	{
		$condition_type = Mage::getStoreConfig('snippets/products/condition');
		if($condition_type) {
			$condition = array();
			if($condition_type == 1) {
				$condition_default = ucfirst(Mage::getStoreConfig('snippets/products/condition_default'));
				if($condition_default) {
					$condition['url'] = 'http://schema.org/' . $condition_default . 'Condition';				
					$condition['text'] = Mage::helper('snippets')->__($condition_default);
				}			
			}
			if($condition_type == 2) {
				$product_condition = '';
				if(!empty($simple)) {
					$product_condition = ucfirst($this->getProductCondition($simple));									
				}
				if(!empty($product_condition)) {
					$product_condition = ucfirst($this->getProductCondition($product));									
				}
				if($product_condition) {
					$condition['url'] = 'http://schema.org/' . $product_condition . 'Condition';				
					$condition['text'] = Mage::helper('snippets')->__($product_condition);
				}			
			}			
			return $condition;		
		}
		return false;		
	}
	
	public function getProductThumbnail($product) 
	{
		Mage::helper('catalog/image')->init($product, 'small_image')->resize(75);
	}
	
	public function getProductImage($product, $simple = null) 
	{		
		$image = '';
		if(!empty($simple)) {
			if($simple->getImage() != 'no_selection') {
				$image = Mage::getModel('catalog/product_media_config')->getMediaUrl($simple->getImage());
			}	
		}
		if(empty($image)) {
			if($product->getImage() != 'no_selection') {
				$image = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
			}
		}	
		return $image;
	}

	public function getCategoryImage($category) 
	{
		if($image_url = $category->getImageUrl()) {
			return $image_url; 
		}
		return false;	
	}

	public function getCategoryThumbnail($category) 
	{
		if($image_url = $category->getThumbnail()) {
			return Mage::getBaseUrl('media') . 'catalog/category/' . $image_url; 
		}
		return false;		
	}		
				
	public function getProductOffers($product, $simple_price = null) 
	{
		$price = ''; $offers = array();
		if(Mage::getStoreConfig('snippets/products/prices') == 'custom') {
			$attribute = Mage::getStoreConfig('snippets/products/price_attribute');
			$price = $product[$attribute];			
		} else {	
			if($product->getTypeId() == 'grouped') {
				if($price = $this->getPriceGrouped($product)) {				
					$offers['price_low'] = Mage::helper('core')->currency($price, true, false);	
					$offers['clean_low'] = Mage::helper('core')->currency($price, false, false);
				}			
			}
			if($product->getTypeId() == 'bundle') {
				$price = $this->getPriceBundle($product); 
			}
			if(!empty($simple_price)) {
				$price = $simple_price;
			}
			if(!$price) {
				$price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
			}	
		}
		if(Mage::getStoreConfig('snippets/products/prices') == 'notax') {
			$tax = Mage::getStoreConfig('snippets/products/taxperc');
			if($tax > 0) {
				$price = (($price / (100 + $tax)) * 100);
			}	
		}
		$offers['price_only'] = number_format(Mage::helper('core')->currency($price, false, false), 2, '.', '');	
		$offers['clean'] = number_format(Mage::helper('core')->currency($price, false, false), 2, '.', '');	
		$offers['price'] = Mage::helper('core')->currency($price, true, false);	
		$offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
		$offers['seller'] = Mage::getStoreConfig('snippets/products/seller');
		if(isset($offers['price_low'])) {
			$offers['type'] = 'http://schema.org/AggregateOffer';
		} else {
			$offers['type'] = 'http://schema.org/Offer';		
		}				
		if(Mage::getStoreConfig('snippets/products/mulitple_currencies')) {
			if($config_currencies = Mage::getStoreConfig('snippets/products/currencies')) {
				$currencyModel = Mage::getModel('directory/currency');
				$currencies = $currencyModel->getConfigAllowCurrencies();
                $rates = $currencyModel->getCurrencyRates($offers['currency'], $currencies);
				if(is_array($rates)) {				
					$cur_array = explode(',', $config_currencies);
					foreach($cur_array as $currency) {
						if(isset($rates[$currency])) {
							$price = number_format(($rates[$currency] * $offers['clean']), 2, '.', '');
							$offers['extra_offer'][$currency]['price'] = $price;
							$offers['extra_offer'][$currency]['currency'] = $currency;
						}
					}
				}	
			}
		}
		return $offers;
	}

	public function getPriceGrouped($product) 
	{
		$price = '';
		$_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
		foreach($_associatedProducts as $_item) {
			$price_associated = Mage::helper('tax')->getPrice($_item, $_item->getFinalPrice(), true);
			if(($price_associated < $price) || ($price == '')) {
				$price = $price_associated;
			}
		}		
		if($price > 0) {
			return $price; 
		}	
	}

	public function getPriceBundle($product) 
	{		
		$price = '';
		if(($product->getPriceType() == '1') && ($product->getFinalPrice() > 0)) {
			$price = $product->getFinalPrice();
		} else {
			$priceModel = $product->getPriceModel();
			$block = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
			$options = $block->setProduct($product)->getOptions();
			$price = 0;
			foreach ($options as $option) {
				$selection = $option->getDefaultSelection();
				if($selection === null) { continue; }
				$selection_product_id = $selection->getProductId(); 
				$_resource = Mage::getSingleton('catalog/product')->getResource();
				$final_price = $_resource->getAttributeRawValue($selection_product_id, 'final_price', $storeId);
				$selection_qty = $_resource->getAttributeRawValue($selection_product_id, 'selection_qty', $storeId);
				$price += ($final_price * $selection_qty); 
			}				
		}
		if($price < 0.01) {
			$price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);			
		}
		return $price; 				
	}	

	public function getCategoryOffers($category) 
	{
		$qty = ''; $price = '';
		$offers = array();
		if(Mage::getStoreConfig('snippets/products/prices') == 'custom') {
			$price_attribute = Mage::getStoreConfig('snippets/products/price_attribute');
			$cat_products = Mage::getModel('catalog/product')->getCollection()->addCategoryFilter(Mage::registry('current_category'))->addAttributeToSelect($price_attribute)->addAttributeToFilter($price_attribute, array('gt' => 0))->load();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($cat_products); 
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($cat_products);	
			$qty = $cat_products;
			if(count($cat_products)) {
				$prices = array();			
				foreach($cat_products as $catproduct) {	
					$prices[] = $catproduct[$price_attribute];
				}
				if($prices) {
					$price_low = min($prices);	
					$price_high = max($prices);	
				} 					
			}						
		} else {		
			$magentoVersion = Mage::getVersion();
			if(version_compare($magentoVersion, '1.7.2', '>=')){
				$price_low = Mage::getSingleton('catalog/layer')->getProductCollection()->getMinPrice();
				$price_high = Mage::getSingleton('catalog/layer')->getProductCollection()->getMaxPrice(); 				
				$qty = Mage::getSingleton('catalog/layer')->getProductCollection()->getSize();
				if($price_low < 0.01) {
					$price_low = '0.0001';
				}	
			} else {
				$price_attribute = 'price';
				$category = Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());	
				$productColl = Mage::getModel('catalog/product')->getCollection()->addCategoryFilter($category)
					->addAttributeToFilter('visibility', array('eq' => 4))
					->addAttributeToFilter('status', array('eq' => 1))
					->addAttributeToFilter('price', array('gt' => 0))
					->addAttributeToSort('price', 'asc')
					->addAttributeToSelect('price')
					->setPageSize(1)->load();
				$qty = count($productColl);
				$lowestProductPrice = $productColl->getFirstItem()->getPrice();	
				$price_low = $lowestProductPrice;
			}	
		}
		if(Mage::getStoreConfig('snippets/products/prices') == 'notax') {
			$tax = Mage::getStoreConfig('snippets/products/taxperc');
			if($tax > 0) {
				if($price_low) {
					$price_low = (($price_low / (100 + $tax)) * 100);
				}	
				if($price_high) {
					$price_high = (($price_high / (100 + $tax)) * 100);
				}	
			}	
		}
		if(isset($price_low)) {
			$offers['price_low'] = Mage::helper('core')->formatPrice($price_low, false);
			$offers['clean_low'] = number_format($price_low, 2, '.', '');	
		} 
		if((isset($price_high)) && (Mage::getStoreConfig('snippets/category/prices') == 'range')) {		
			$offers['price_high'] = Mage::helper('core')->formatPrice($price_high, false);
			$offers['clean_high'] = number_format($price_high, 2, '.', '');	
		}
		$offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();		
		$offers['seller'] = Mage::getStoreConfig('snippets/products/seller');
		if(!isset($offers['price_low'])) {
			$offers['price_low'] = Mage::helper('core')->currency('0.00', true, false);		
			$offers['clean_low'] =  '0.00';
		}			
		$offers['qty'] = $qty;		
		return $offers;
	}	
		
	public function getProductDescription($product) 
	{	
		if(Mage::getStoreConfig('snippets/products/description')) {		
			$attribute = Mage::getStoreConfig('snippets/products/description_attribute');
			if($attribute) {
				$description = trim(strip_tags($product[$attribute]));
			} else {
				$description = trim(strip_tags($product->getShortDescription()));		
			}	
			$description_lenght = Mage::getStoreConfig('snippets/products/description_lenght');
			if($description_lenght > 0) {
				$description = Mage::helper('core/string')->truncate($description, $description_lenght, '...', $remainder, false);
			}
			return $description;
		}
		return false;		
	}    

	public function getCategoryDescription($category) 
	{	
		if(Mage::getStoreConfig('snippets/category/description')) {		
			$description = strip_tags($category->getDescription());		
			if($description) {
				$description_lenght = Mage::getStoreConfig('snippets/category/description_lenght');			
				if($description_lenght > 0) {
					$description = Mage::helper('core/string')->truncate($description, $description_lenght, '...', $remainder, false);
				}			
				return $description; 
			}	
		}
		return false;		
	} 
	
	public function getProductRatings($product) 
	{			
		$show_reviews = Mage::getStoreConfig('snippets/products/reviews'); 
		if($show_reviews) {		
			if((Mage::getStoreConfig('snippets/products/reviews_source') == 'yotpo') && Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo')) { 			
				return $this->getYotpoReviews();
			} else { 	
				$summaryData = Mage::getModel('review/review_summary')->setStoreId(Mage::app()->getStore()->getStoreId())->load($product->getId());
				$rating = array();
				$rating['count'] = $summaryData->getReviewsCount();  
				$rating['percentage'] = $summaryData->getRatingSummary();
				if(Mage::getStoreConfig('snippets/products/reviews_metric') == '5') {		
					$rating['avg'] = round(($summaryData->getRatingSummary() / 20), 1);
					$rating['best'] = '5';
				} else {
					$rating['avg'] = round($summaryData->getRatingSummary());
					$rating['best'] = '100';		
				}
				if(Mage::getStoreConfig('snippets/products/reviews_type') == 'votes') {		
					$rating['type'] = 'ratingCount';
				} else {
					$rating['type'] = 'reviewCount';		
				}	
				if($summaryData->getReviewsCount() > 0) {
					return $rating;
				}			
			}
		}
		return false;		
	}  

	public function getYotpoReviews() 
	{	
		$rating = array();
		$yotpo_snippets = Mage::helper('yotpo/richSnippets')->getRichSnippet(); 			
		if(isset($yotpo_snippets["average_score"])) {
			if($yotpo_snippets["average_score"] > 0) {
				$ratingSummary = $yotpo_snippets["average_score"];
				$rating['percentage'] = ($ratingSummary * 20);
				$rating['avg'] = $ratingSummary;
				$rating['count'] = $yotpo_snippets["reviews_count"];	
				$rating['best'] = '5';										
				$rating['type'] = 'ratingCount';
				if(Mage::getStoreConfig('snippets/products/reviews_metric') != '5') {		
					$rating['avg'] = ($ratingSummary * 20);
					$rating['best'] = '100';
				}
				return $rating;
			}
		}
		return $rating;		
    }
    
	public function getCategoryRatings($category) 
	{			
		$enabled = Mage::getStoreConfig('snippets/category/reviews'); 
		if($enabled) {					
			$count = ''; $ratingSummary = '';			
			$cacheKey = 'ratings-snippets-' . $category->getId() . '-' . Mage::app()->getStore()->getId();
			if($cat_ratings = @unserialize(Mage::app()->getCacheInstance()->load($cacheKey))) {
				$count = $cat_ratings['reviews_count'];
				$ratingSummary = ($cat_ratings['rating_summary'] / $cat_ratings['qty']);
			} else {
				$productIds = $category->getProductCollection()->addAttributeToFilter('visibility', array('neq' => 1))->addAttributeToFilter('status', 1)->getAllIds();
				if($productIds) {
					$resource = Mage::getSingleton('core/resource');
					$readConnection = $resource->getConnection('core_read');
					$review_entity_summary_table = $resource ->getTableName('review_entity_summary');
					$exists = (boolean)Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($review_entity_summary_table);
					if($exists) {		
						$query = "SELECT SUM(rating_summary) as 'rating_summary', SUM(reviews_count) as 'reviews_count', COUNT(entity_pk_value) as 'qty' FROM " . $review_entity_summary_table . " WHERE store_id = " . Mage::app()->getStore()->getId() . " AND rating_summary > 0 AND reviews_count > 0 AND entity_pk_value IN (" . implode(',', array_map("intval", $productIds)) . ")";
						$cat_ratings = $readConnection->fetchRow($query);
						if(!empty($cat_ratings['rating_summary']) && !empty($cat_ratings['reviews_count'])) {			
							$count = $cat_ratings['reviews_count'];
							$ratingSummary = ($cat_ratings['rating_summary'] / $cat_ratings['qty']);
							$cacheData = serialize($cat_ratings);
							$cacheKey = 'ratings-snippets-' . $category->getId() . '-' . Mage::app()->getStore()->getId();
							Mage::app()->getCacheInstance()->save($cacheData, $cacheKey);
						} 
					}
				}
			}					
			if(empty($count)) {
				return false;
			}
			$rating = array();
			$rating['count'] = $count;
			$rating['percentage'] = ($ratingSummary);
			if(Mage::getStoreConfig('snippets/category/reviews_metric') == '5') {		
				$rating['avg'] = round(($ratingSummary / 20), 1);
				$rating['best'] = '5';
			} else {
				$rating['avg'] = round($ratingSummary);
				$rating['best'] = '100';		
			}
			if(Mage::getStoreConfig('snippets/category/reviews_type') == 'votes') {		
				$rating['type'] = 'ratingCount';
			} else {
				$rating['type'] = 'reviewCount';		
			}				
			return $rating;
		}
		return false;
	}
		
	public function getCurrentUrl() 
	{	
		$url = Mage::helper('core/url')->getCurrentUrl(); 
		$url = preg_replace('/\?.*/', '', $url);		
        return $url;
    }	
	
	public function getProductExtraFields($product, $simple = null) 
	{
		$fields = array();
		$attributes_arr = @unserialize(Mage::getStoreConfig('snippets/products/attributes'));
		$custom_arr = @unserialize(Mage::getStoreConfig('snippets/products/cus_attributes'));
		$fields_arr = array_merge($attributes_arr, $custom_arr);
		if(count($fields_arr)) {
			foreach($fields_arr as $field) {
				$value = '';
				if(!empty($simple)) {
					$value = $this->getProductAttributeValue($simple, $field);
				}
				if(empty($value)) {
					$value = $this->getProductAttributeValue($product, $field);
				}
				if(!empty($value)) {
					if($field['type'] == 'brand') {
						$data = '<span itemprop="brand" itemscope itemtype="http://schema.org/Brand"><span itemprop="name">' . $value . '</span></span>';
						$fields[] = array('value'=> $data, 'label'=> 'Brand', 'clean'=> $value, 'itemprop'=> 'brand');			
					} else {
						$data = '<span itemprop="' . $field['type'] . '">' . $value . '</span>';
						$fields[] = array('value'=> $data, 'label'=> ucfirst($field['type']), 'clean'=> $value, 'itemprop'=> $field['type']);			
					}				
				}
			}	
		}
		return $fields;
	}

	public function getProductCondition($product) 
	{		
		$condition_show = Mage::getStoreConfig('snippets/products/condition');
		if($condition_show) {							
			$attribute = Mage::getStoreConfig('snippets/products/condition_attribute');		
			if($condition = $product->getAttributeText($attribute)) {
				if(!is_array($condition)) {
					return $condition;
				}	
			} else {
				if($condition = $product[$attribute]) {
					return $condition;				
				}	
			}
		}
		return false;
	}
	
	public function getMarkup() 
	{
		if(Mage::registry('product')) {
			return Mage::getStoreConfig('snippets/products/type');
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			return Mage::getStoreConfig('snippets/category/type');		
		}			
	}	
	
	public function getContent() 
	{
		if(Mage::registry('current_product')) {
			$type = Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				if(Mage::getStoreConfig('snippets/products/location') == 'advanced') {
					return Mage::getStoreConfig('snippets/products/location_custom');			
				} else {
					return Mage::getStoreConfig('snippets/products/location');		
				}
			}
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/products/location_ft');
			}
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			$type = Mage::getStoreConfig('snippets/category/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/category/location');		
			}
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/category/location_ft');
			}
		}
	}	

	public function getPosition() 
	{
		if(Mage::registry('current_product')) {
			$type = Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/products/position');
			}	
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/products/position_ft');
			}	
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			$type = Mage::getStoreConfig('snippets/category/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/category/position');
			}	
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/category/position_ft');
			}	
		}
	}	
	
	public function getEnabled() 
	{
		$enabled = Mage::getStoreConfig('snippets/general/enabled');
		$block = ''; $enabled_ent = '';	 $type = '';			
		if(Mage::registry('current_product')) {
			$enabled_ent 	= Mage::getStoreConfig('snippets/products/enabled');
			$type 			= Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				$block 	= Mage::getStoreConfig('snippets/products/location');
			}
			if($type == 'footer') {
				$block 	= Mage::getStoreConfig('snippets/products/location_ft');
			}
		}elseif(Mage::registry('current_category')) {
			$enabled_ent = Mage::getStoreConfig('snippets/category/enabled');			
			$type = Mage::getStoreConfig('snippets/category/type');	
			if($type == 'visible') {
				$block 	= Mage::getStoreConfig('snippets/category/location');
			}
			if($type == 'footer') {
				$block 	= Mage::getStoreConfig('snippets/category/location_ft');
			}	
		}
		if(($block == '') || ($enabled == '') || ($enabled_ent == '') || ($type == 'hidden')) {
			return false;
		} else {
			return true;
		}
	}	

	public function getFirstBreadcrumbTitle($title) 
	{
		$custom = Mage::getStoreConfig('snippets/system/breadcrumbs_custom'); 
		$enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');			
		$customname = Mage::getStoreConfig('snippets/system/breadcrumbs_customname'); 
		if($custom && $enabled && $customname) {
			return $customname;
		} 		
		return $title;		
	}

	public function getFilterHash() 
	{
		if($category = $this->getCategory()) {		
			$url = str_replace('?___SID=U', '', $category->getUrl());
			$diff = str_replace(Mage::getBaseUrl(), '', $url);
			return $diff;					
		}	
	}

	public function getProductAttributeValue($product, $data) 
	{
		$value = '';
		$input_type = $data['input_type'];
		$source = $data['source'];
		$type = $data['type'];
		if($input_type == 'select') {
			$value = $product->getAttributeText($source);
		} elseif($input_type == 'multiselect') {
			if(count($product->getAttributeText($source))) {				
				if(count($product->getAttributeText($source)) > 1) {
					$value = implode(',', $product->getAttributeText($source));
				} else {
					$value = $product->getAttributeText($source);			
				}
			}
		} else {
			if(isset($product[$source])) {					
				$value = $product[$source];	
			}		
		}
		
		if(!empty($value)) {
			if($type == 'gtin8') {
				$value = str_pad($value, 8, "0", STR_PAD_LEFT);		
			}
			if($type == 'gtin12') {
				$value = str_pad($value, 12, "0", STR_PAD_LEFT);		
				$type_text = $type;				
			}
			if($type == 'gtin13') {
				$value = str_pad($value, 13, "0", STR_PAD_LEFT);		
				$type_text = $type;				
			}
			if($type == 'gtin14') {
				$value = str_pad($value, 14, "0", STR_PAD_LEFT);				
			}
		}
		return $this->escapeHtml($value);
	}
			   
}