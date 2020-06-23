<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<h1>{_p var='contact_importer'}</h1>
{_p var='block_all_further_email_messages' signup=$SignUp login=$login}
<p>{_p var='you_can_click'} <a  title="Subscribe Your Email" class="inlinePopup" href="#?call=contactimporter.subscribe&amp;width=300&amp;height=300&amp;email={$email}">{_p var='subscribe'}</a> {_p var='to_receive_the_further_email_messages'} </p>
