<?php
?>
<div class="container">
{$aItem.embed_code}
</div>
{literal}
    <style>
        .container {
            position: relative;
            width: 100%;
            padding-bottom: 65.25%;
        }
        video,
        iframe {
            position: absolute;
            top: 50%;
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            left: 0;
            width: 100%;
            height: 100%;
            border: none !important;
        }
    </style>
{/literal}
