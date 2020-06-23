<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gettingstarted_component_block_Email_Settings extends Phpfox_Component{
    public function process()
    {     
        $this->template()->assign(array(
           'sHeader' => 'Email Settings'
        ));
        
        return 'block';
    }
}
?>
