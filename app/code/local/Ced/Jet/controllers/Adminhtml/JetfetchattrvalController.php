<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_Jet
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
  
class Ced_Jet_Adminhtml_JetfetchattrvalController extends Mage_Adminhtml_Controller_Action{

public function fetchAction()
	{
			 $name = $this->getRequest()->getPost('mag_att_code');
    $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($name)->getFirstItem();
    $attributeId = $attributeInfo->getAttributeId();
    $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    $attributeOptions = $attribute ->getSource()->getAllOptions(false); 
    //print_r($attributeOptions);die();


			$mjetattr_id = $this->getRequest()->getPost('jet_id');
			//test code start	

			 $units_or_options = array();
            $csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute.csv";

            if (!file_exists($file)) { ?>

            	<strong>Note:</strong></label>
				<p>Jet Extension Csv missing please check "Jet_Taxonomy_attribute.csv" exist at "var/jetcsv" location.</p>
               
               <?php return;
            }

            $taxonomy = $csv->getData($file);
            unset($taxonomy[0]);

            $save_attr_id = false;
            $save_attr_unitType = false;

            foreach ($taxonomy as $txt) {
                if (number_format($txt[0], 0, '', '') == $mjetattr_id) {

                    $save_attr_id = number_format($txt[0], 0, '', '');
                    $save_attr_unitType = $txt[3];
                    break;
                }
            }

            if ($save_attr_id == false) { ?>
                <strong>Note:</strong></label>
				<p>Jet Atrribute id: <?php echo $mjetattr_id ?> which you trying to map is not available  in jet.com. </p>
               <?php return;
            }

			//test code end 
			$csv = new Varien_File_Csv();
            $file = Mage::getBaseDir("var") . DS . "jetcsv" . DS . "Jet_Taxonomy_attribute_value.csv";


            if (!file_exists($file)) {?>
            		<strong>Note:</strong></label>
				<p>Jet Extension Csv missing please check "Jet_Taxonomy_attribute_value.csv" exist at "var/jetcsv" location.</p>
					<?php  return;
            }
            $taxonomy = $csv->getData($file);


            unset($taxonomy[0]);
            try {
                if ($save_attr_unitType == 2) {
                    foreach ($taxonomy as $txt) {
                        $numberfomat_id = number_format($txt[0], 0, '', '');

                        if ($mjetattr_id == $numberfomat_id) {

                            $units_or_options[] = $txt[2];
                        }
                    }
                } else if ($save_attr_unitType == 0) {

                    foreach ($taxonomy as $txt) {
                        if ($mjetattr_id == number_format($txt[0], 0, '', '')) {
                            $units_or_options[] = $txt[1];
                        }
                    }

                }
            } catch (Exception $e) { ?>

            <strong>Note:</strong></label>
				<p> <?php $e->getMessage() ?> </p>
             <?php  return;
            }
            //test s

	   $options_array = $units_or_options;?>
	  <div class="hor-scroll">
		
			<?php if($save_attr_unitType == 2){ ?>
			
				<label"><strong>Note:</strong></label>
				<p>Jet Atrribute id: <?php echo $mjetattr_id ?> which you trying to map is a <b>UNIT</b> type attribute in jet.com. You need to Add or Create new options based on these values in your Drop down options </p>
				<p>Options: <b><?php echo $row['unit'] ?></b></p>
				<label>Example: <strong>"Your value"{space}"UNIT"</strong></label>
				<p>We have taken <b>10</b> as Value for example.</p> 
					<select>
						<?php foreach ($options_array as $data) { ?>
							<option value="<?php echo '10 '.$data; ?>"><?php echo '10 '.$data; ?></option>
						<?php } ?> 
					</select> 
				</p>
		  <?php 
			  } 
		else if($save_attr_unitType == 0) {  ?>
			<?php if(count($options_array)>0){ ?>
				<label"><strong>Note:</strong></label>
				<p>Jet Atrribute id: <?php echo $mjetattr_id ?> which you trying to map is a <b>Dropdown</b> type attribute  & the options of this attribute is fixed on jet.com You need to Add or Create new options under Manage Label / Options tab based on these values </p>
				<p>Options: <b><?php foreach ($options_array as $data) { ?>
							<?php echo $data; ?>,
						<?php } ?> </b></p>
				<label>Magento Attribute Value
				<select id ="mag_attr_vals">
						<option value="">Please select Options</option>
						<?php foreach ($attributeOptions as $data) { 
							if($data['label']!='Admin' && $data['label']!='Main Website')
							{?>
									<option value="<?php echo $data['label']; ?>"><?php echo $data['label']; ?></option>
							<?php }
							 } ?> 
					</select> Mapped with this Jet Attribute Value
					<select id ="jet_attr_vals">
						<option value="">Please select Options</option>
						<?php foreach ($options_array as $data) { ?>
							<option value="<?php echo $data; ?>"><?php echo $data; ?></option>
						<?php } ?> 
					</select> 
					
					<button style="" onclick ="saveMapping()" id ="save_map" class="scalable " type="button" title="Save Mapping"><span><span><span>Save Mapping</span></span></span></button>
				</p>
			<?php }
			else { ?>
			<strong>Note:</strong></label>
				<p>Jet Atrribute id: <?php echo $mjetattr_id ?> which you trying to map is not available  in jet.com. </p>
				<?php }
			}
			?>
			
		
	  </div>
	  <?php 
            //test e
	}
	public function mapAction()
	{
		$arr = array();
		$updated_data = '';
		$updated_data1 = '';
		$mjetattr_id = trim($this->getRequest()->getPost('jet_id'));
		$jetattrval = trim($this->getRequest()->getPost('jetattrval'));
		$magattrval = trim($this->getRequest()->getPost('magattrval'));
		$jetattribute = Mage::getModel('jet/jetattribute');
        $collection = $jetattribute->getCollection()->addFieldToFilter('jet_attr_id', $mjetattr_id)->getData();

        $updated_data = json_decode($collection[0]['jet_attr_val'],true);
        $arr = $updated_data;
        if($jetattrval!='' && $magattrval!='' && $magattrval!='-- Please Select --')
        {
        	$arr[$magattrval]=$jetattrval;
        	
   		} 
   		
       
        $jetattribute = Mage::getModel('jet/jetattribute')->load($collection[0][id]);
        $jetattribute->setData('jet_attr_val',json_encode($arr));
       	$jetattribute->save();
       	?>
       	<table border="2px">
	  <th>Magento Attribute Value</th>
	  <th>Jet Attribute Value</th>
	 <th>Action</th>
	  <?php 
	  $jetattribute = Mage::getModel('jet/jetattribute');
	$collection = $jetattribute->getCollection()->addFieldToFilter('jet_attr_id', $mjetattr_id)->getData();

        $updated_data = json_decode($collection[0]['jet_attr_val'],true);

	  foreach($updated_data as $keyy=>$vall)
	  { ?>
	   <tr>
	  <td><?php echo $keyy;?> </td>
	  <td><?php echo $vall;?> </td>
	  <td><button onclick ="deleteMapping('<?php echo $keyy ?>')" class="scalable" id ="delete_attr_val" type="button" title="Delete"><span><span><span>Delete</span></span></span></button></td>
	  </tr>
	  <?php	}?>
	  
	  </table>
	<?php }
	public function deleteAction()
	{
		$arr = array();
		$mjetattr_id = $this->getRequest()->getPost('jet_id');
		$del_val = $this->getRequest()->getPost('jet_val');

		$jetattribute = Mage::getModel('jet/jetattribute');
        $collection = $jetattribute->getCollection()->addFieldToFilter('jet_attr_id', $mjetattr_id)->getData();

        $updated_data = json_decode($collection[0]['jet_attr_val'],true);
        foreach ($updated_data as $key11 => $value11) {
        	if($key11 == $del_val)
        	{
        		unset($updated_data[$key11]);
        	}
        }
        $arr = $updated_data;
       $jetattribute = Mage::getModel('jet/jetattribute')->load($collection[0][id]);
        $jetattribute->setData('jet_attr_val',json_encode($arr));
       	$jetattribute->save();
       	?>
       	<table border="2px">
	  <th>Magento Attribute Value</th>
	  <th>Jet Attribute Value</th>
	 	<th>Action</th>
	  <?php 
	  $jetattribute = Mage::getModel('jet/jetattribute');
	$collection = $jetattribute->getCollection()->addFieldToFilter('jet_attr_id', $mjetattr_id)->getData();

        $updated_data = json_decode($collection[0]['jet_attr_val'],true);

	  foreach($updated_data as $keyy=>$vall)
	  { ?>
	   <tr>
	  <td><?php echo $keyy;?> </td>
	  <td><?php echo $vall;?> </td>
	  <td><button onclick ="deleteMapping('<?php echo $keyy ?>')" class="scalable" id ="delete_attr_val" type="button" title="Delete"><span><span><span>Delete</span></span></span></button></td>
	  </tr>
	  <?php	}?>
	  
	  </table>
	  <?php
	}
	public function noticeAction()
	{
		$globalnotice ='read'; 
		Mage::getSingleton('core/session')->setGlobalnotice($globalnotice);
	}
}
?>	

