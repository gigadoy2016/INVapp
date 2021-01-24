<?php
class Category extends AppModel{
    var $name='Category';
    
    var $validate = array(
        'name'  => VALID_NOT_EMPTY,
        'serial_id'   => VALID_NOT_EMPTY
    );

}
?>