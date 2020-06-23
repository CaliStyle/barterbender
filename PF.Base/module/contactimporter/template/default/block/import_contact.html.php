<h3 style="border:none">{_p var='import_email_contact_list'}</h3>
{if isset($require_invite) && $require_invite}
    <p style="padding:10px;background-color:#EDEFF4">{_p var='require_invite_your_friend_before_using'}</p>
    <br/>
{/if}
{if isset($signup_success) && $signup_success}
    <p style="padding:10px;background-color:#EDEFF4">{_p var='contactimporter_invite_congratulation'}</p>
    <br/>
{/if}

{if isset($plugType)}
    {if isset($errors) and count($errors) > 0}
        {foreach from= $errors  item=er}
            <div id="errros_div_h">
                <ul class='form-errors'>
                    <li>
                        <ul class='errors'>
                            <li>{$er}</li>
                        </ul>

                    </li>
                </ul>
            </div>
        {/foreach}
    {/if}
{/if}

<div class="mail_list">
    {foreach from=$provider_lists item = email}
        {if $email.logo !=''}
            <div>
                <span id="form_{$email.name}"></span>
                {if $email.name eq 'yahoo'}
                    <a id="yahoo" href="#?call=contactimporter.callYahoo&amp;height=80&amp;width=270"
                       class=" inlinePopup usingapi" title="{_p var='yahoo_contacts'}">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {elseif $email.name eq 'hotmail'}
                    <a id="linkedinA" href="#?call=contactimporter.callHotmail&amp;height=80&amp;width=270"
                       class="inlinePopup usingapi" title="{_p var='hotmail_authorization'}">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {elseif $email.name eq 'linkedin'}
                    <a id="linkedinA" href="#?call=contactimporter.callLinkedIn&amp;height=80&amp;width=270"
                       class="inlinePopup usingapi" title="{_p var='linkedin_authorization'}">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {elseif $email.name eq 'twitter'}
                    <a id="twitterA" href="#?call=contactimporter.callTwitter&amp;height=80&amp;width=270"
                       class="inlinePopup usingapi" title="{_p var='twitter_authorization'}">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {elseif $email.name eq 'facebook_'}
                    {if $fbAIP}
                        <a id="fbApi" href="javascript: invite_facebook_open()" class="inlinePopup usingapi"
                           title="{_p var='facebook_authorization'}">
                            <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                            <span class="title">{$email.title}</span>
                        </a>
                    {/if}
                {elseif $email.name eq 'gmail'}
                    <a id="gmail" href="#?call=contactimporter.callGmail&amp;height=80&amp;width=270"
                       class=" inlinePopup usingapi" title="{_p var='gmail_contacts'}">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {else}
                    <a id="{$email.name}" title="{$email.title} {_p var='import_your_contacts'}"
                       href="#?call=contactimporter.callImporterForm&amp;height=150&amp;width=400&amp;provider_type={$email.type}&amp;default_domain={$email.default_domain}&amp;provider_box={$email.name}"
                       class="inlinePopup usingapi">
                        <img src="{$core_url}module/contactimporter/static/image/{$email.logo}_status_up.png"/>
                        <span class="title">{$email.title}</span>
                    </a>
                {/if}
            </div>
        {/if}
    {/foreach}
    <div>
        <a href="#?call=contactimporter.callCsv&amp;height=80&amp;width=500" id="CSV" class="inlinePopup usingapi"
           title="{_p var='upload_file_csv'}">
            <img src="{$core_url}module/contactimporter/static/image/csv_status_up.png"/>
            <span class="title">{_p var='upload_file_csv'}</span>
        </a>
    </div>
    <div>
        <a href="#?call=contactimporter.callTypingmanual&amp;height=80&amp;width=500" id="typingmanual"
           class="inlinePopup usingapi" title="{_p var='inviete_by_manually'}">
            <img src="{$core_url}module/contactimporter/static/image/manual_status_up.png"/>
            <span class="title">{_p var='inviete_by_manually'}</span>
        </a>
    </div>

</div>

{literal}
<script type="text/javascript">
    window.fbAsyncInit = function () {
        FB.init({
            appId: '{/literal}{$fbAIP}{literal}',
            xfbml: true,
            version: 'v2.0'
        });
        open_facebook_invite_dialog();
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }
    (document, 'script', 'facebook-jssdk'));

    function open_facebook_invite_dialog() {
        if (typeof FB == 'undefined')
            return;
    }

    function invite_facebook_open() {
        var date = new Date();
        var timestamp = date.getTime();
        FB.ui({
            method: 'send',
            link: '{/literal}{$facebookInviteLink}{literal}',
        }, function (res) {
            if (res.success) {
                $.ajaxCall('contactimporter.fbInviteSuccessfull');
            }
        });
    }
</script>
{/literal}
