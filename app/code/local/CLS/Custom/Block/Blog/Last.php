<?php
/**
 * Last.php
 *
 * @category    CLS
 * @package     Custom
 * @author      Chris Huffman <chris.huffman@classyllama.com>
 * @copyright   Copyright (c) 2015 Classy Llama Studios, LLC
 */

class CLS_Custom_Block_Blog_Last extends Mage_Core_Block_Template
{
    const XML_PATH_POST_COUNT = 'catalog/product_view_display/blog_post_count';

    /**
     * Get the most recent blog posts, limited by config setting, and filtered by tag
     *
     * @return mixed
     */
    public function getRecent()
    {
        //get tags to search for
        $tagSet = $this->getTags();
        $count = Mage::getStoreConfig(self::XML_PATH_POST_COUNT);

        //get collection of published posts
        $connection = Mage::helper('wordpress/database')->getReadAdapter();

        //query the wp db to get post ids of the posts matching that tags that were set
        $select = $connection->select()
            ->from(array('p' => 'wp_posts'), 'p.ID')
            ->join(array('r' => 'wp_term_relationships'), new \Zend_Db_Expr('r.object_id = p.ID'), '')
            ->join(array('tt' => 'wp_term_taxonomy'), new \Zend_Db_Expr('tt.term_taxonomy_id = r.term_taxonomy_id'), '')
            ->join(array('t' => 'wp_terms'), new \Zend_Db_Expr('t.term_id = tt.term_id'), '')
            ->where('p.post_status = "publish"')
            ->where('t.name in (?)', $tagSet)
            ->order('p.post_date DESC')
            ->limit($count);

        $postIdsList = $connection->fetchAll($select);

        $postIds = array();

        foreach($postIdsList as $id)
        {
            $postIds[] = $id['ID'];
        }

        //get collection of posts retrieved from the db query

        $collection = Mage::getModel('wordpress/post')->getCollection()
            ->addFieldToFilter('id', array('in' => $postIds));

        //return any matching posts
        return $collection;
    }

    /**
     * @param $tags
     */
    public function setTags($tags)
    {
        $this->setData('tags', $tags);
    }

    /**
     * Check post content to see if it contains an image, if so, return the first one
     *
     * @param $post
     * @return mixed
     */
    public function getPostImage($post)
    {
        $content = $post->getPostContent();
        preg_match('/(<img.*?\/>)/s', $content, $matches);

        if(!empty($matches)) {
            return $matches[0];
        }
    }
}