/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Jet
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

var id = '';
function uploadProduct(e,product_id,url){
    
    var skin_path = url.split('/index.php');
   var cont_path = url;
    //e.preventDefault();

    $('loading-mask').setStyle({zIndex: '-500'});
    /*jQuery('#savereal_'+newtext).remove(); */
    //console.log($('manage_'+product_id).select('img'));

    id = product_id;

    $('upload_'+product_id).hide();

    var loader = 'loader_'+product_id;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '50%' , 'height' : '50%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    $('manage_'+product_id).insert(content);
    var post = [product_id];
   new Ajax.Request(cont_path+'adminhtml_jetproduct/directapiupload',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  entity_id: product_id },
            onSuccess: function(transport){

                if(transport.responseText.evalJSON(true).error){ 
                    
                  err_msg = transport.responseText.evalJSON(true).error;
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<a id="error_'+product_id+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_id+')\'>Error</a>' );
                    
                }else{ 
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<span style="color : green" id ="success_'+product_id+'">success</span>' );
                }

                },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

function dialogBox(type , msg , product_id) {
    if(type == 'error'){
            jetPopup = new Window({
                id:"popup_window",
                className: "magento",
                windowClassName: "popup-window",
                title: "Error Uploading Product !! ",
                draggable:true,
                wiredDrag: true,
                width: 300,
                height: 126,
                minimizable: false,
                maximizable: false,
                showEffectOptions: {
                    duration: 0.4
                },
                hideEffectOptions:{
                    duration: 0.4
                },
                destroyOnClose: true,
                OnClose : confirmExit
            });
        jetPopup.getContent().innerHTML='<span style="color: #df280a">'+msg+'</<style>';
        jetPopup.setZIndex(100);
        jetPopup.showCenter(true);
    }
    Event.observe($('popup_window_close'), 'click', confirmExit);
}

    function confirmExit(){
       
          $('error_'+id).remove();
        $('upload_'+id).setStyle({display : 'block'});  
        
    }
    function saveTrigger(url){


        
         var data=document.getElementsByClassName("saveall");
        var ids=new Array();
        for (i = 0; i < data.length; i++){
            var arr=data[i]['id'].split('_');
           ids.push(arr[1]);
           
        }
        //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
             
     //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_saveallnext(id[zz]);
       //}
        
        manageproducts_saveallnext(url);
        function manageproducts_saveallnext(url) {
             //var new_idss = ids+'';
        //var id = new Array();
        //id = ids.toString().split(',');
        //for(var zz = 0;zz<ids.length;zz++)
       //{
         //    manageproducts_saveallnext(id[zz]);
       //}
             var iddd = ids.shift();
                if(iddd)
                   uploadoneProduct('event',iddd,true,url);
        }

        function uploadoneProduct(e,product_id,saveall,url){
    
            var skin_path = url.split('/index.php');
            var cont_path = url;
            
    //e.preventDefault();

    $('loading-mask').setStyle({zIndex: '-500'});
    /*jQuery('#savereal_'+newtext).remove(); */
    //console.log($('manage_'+product_id).select('img'));

    id = product_id;

    $('upload_'+product_id).hide();

    var loader = 'loader_'+product_id;
    var content = new Element('img' , {'id' : loader , 'style' : 'display:table ; padding-left : 10px ; padding-top : 7px' , 'width' : '50%' , 'height' : '50%' , 'alt' : 'loading...' ,'src' : skin_path[0]+'/skin/adminhtml/default/default/images/ajax-loader-tr.gif' });
    if (document.contains(document.getElementById('error_'+product_id))) {
        $('error_'+product_id).remove();
    }
     if (document.contains(document.getElementById('success_'+product_id))) {
         $('success_'+product_id).remove();
    }
   
    $('manage_'+product_id).insert(content);
    var post = [product_id];
    new Ajax.Request(cont_path+'adminhtml_jetproduct/directapiupload',
        {
            method:'post',
            parameters: { form_key: FORM_KEY,  entity_id: product_id },
            onSuccess: function(transport){

                    
                     if(saveall){
                            manageproducts_saveallnext(url);
                        }
                 
                if(transport.responseText.evalJSON(true).error){ 
                    
                    
                    err_msg = transport.responseText.evalJSON(true).error;


                    $(loader).remove();
                   
                    $('manage_'+product_id).insert(  '<a id="error_'+product_id+'" href="javascript: void(0);" onclick = \'dialogBox("error" , "'+err_msg+'",'+product_id+')\'>Error</a>' );
                   
                }else{ 
                    $(loader).remove();
                    $('manage_'+product_id).insert(  '<span style="color : green" id ="success_'+product_id+'">success</span>' );
                }



            },

            onFailure: function(){
              alert('Error');
              $(loader).remove();
                }
        });
    

}

    } 