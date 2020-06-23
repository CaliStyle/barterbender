<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<h1>{_p var='export_your_friend_contacts'}</h1>
{if Phpfox::getUserParam('contactimporter.export_contact', false)}
    <p>{_p var='if_you_have_posts_in_another_system_you_can_export_those_into_this_site_to_get_started_choose_a_s'}:</p>
    <div>
       <table style="background-color:#FFFFFF;border-color:#DFDFDF;-moz-border-radius:4px 4px 4px 4px; border-spacing:0;border-style:solid;border-width:1px;clear:both;margin:0;width:100%;" cellspacing='0'  >
           <tbody>
           <tr style="background-color:#F9F9F9;">
               <td style="padding:6px 15px;font-size:12px !important; font-weight:bold;  border-color:#DFDFDF;border-bottom-style:solid; border-bottom-width:1px;">
                   <a title="{_p var='export_contact_to_csv_file'}" href="javascript:void(0);" onclick="window.location.href='{$url}';return false;">{_p var='export_contacts'}</a>
               </td>
               <td style="padding:6px 15px;border-color:#DFDFDF;border-bottom-style:solid; border-bottom-width:1px;">{_p var='export_your_friend_contacts_to_csv_file'}.</td>
           </tr>
           </tbody>
       </table>
    </div>
{else}
    <div class="error_message ">{_p var='you_do_not_have_permission_to_export_contact'}</div>
{/if}