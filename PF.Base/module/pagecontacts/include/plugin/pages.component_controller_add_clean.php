<?php
$iId = phpfox::getLib('request')->get('id');

$sTopicCount = _p('pagecontacts.topic_name_count');
$bIsOwnerPage = false;

if(isset($iId) && $iId)
{
    $bIsOwnerPage = phpfox::getService('pagecontacts')->isOwnerPage($iId);
}
?>
<script type="text/javascript" language="javascript">
    $Behavior.onLoadPluginContact = function() {
   <?php	
if(Phpfox::getUserParam('pagecontacts.can_create_page_contact') && $bIsOwnerPage)
{
?>
        var bIsContactFirstRun = false;
         $(".page_section_menu").find("li").find("a").click(function(evt) {
            $('#js_pages_add_holder').find(".contact").hide();
        });  
        
        if (($("#content").find("ul").first().find("#contact_tab").length == 0))
        {
            $("#content").find(" ._block_error .page_section_menu ul").first().append("<li><a ref=\"contact_tab\" id=\"contact_tab\" href=\"#\" onclick=\"showContact(this); return false;\"><?php echo _p('pagecontacts.contact_setting'); ?></a></li>");
            $('#js_pages_add_holder').find("form").prepend($("<div class=\"contact\" style=\"display: none;\">"));
            <?php if ($this->request()->get('tab') == 'contact') { ?>     
                  $("#contact_tab").click(); 
                  bIsContactFirstRun = true; 
            <?php } ?>
        }
        
        $Core.pagecontacts.init({sRequired:"*", isAdd: true, bErrors: true, iMaxAnswers: 50, iMinAnswers: 0, iMaxQuestions: 50, iMinQuestions: 1});
        
        $('#js_add_question').click(function()
        {
            $Core.pagecontacts.addQuestion();
            return false;
        });
        
        if($("#donation_tab").size() > 0)
        {
            if($("#donation_tab").attr("onclick").indexOf("$(\".contact\").hide();") < 0) {
                $("#donation_tab").attr({
                    "onclick": ("$(\".contact\").hide();" + $("#donation_tab").attr("onclick"))
                });
            }
        }
        <?php
}
?>

    };
    
    function showContact(obj){
        $this = $(obj); 
        $this.unbind();
        $('#js_pages_add_holder').find(".contact").load($.ajaxBox('pagecontacts.config','iPageId=<?php echo $iId; ?>'));
        $('#js_pages_add_holder').find(".contact").show();
        $('#js_pages_add_holder').find(".js_pages_block").hide();
        $('#js_pages_add_holder').find(".donation").hide();
        $this.parents("ul").find(".active").removeClass("active");
        $this.parent().addClass("active");
    }
    
    $Core.pagecontacts =
    {
        aParams: {},
        iTotalQuestions : 1,

        init: function(aParams)
        {
            this.aParams = aParams;    
            if ($Core.pagecontacts.aParams.isAdd == true)
            {
                $(document).ready(function()
                {
                    if ($Core.pagecontacts.aParams.bErrors == false)
                    {
                        for (i = 0; i < $Core.pagecontacts.aParams.iMinQuestions; i++)
                        {
                            $Core.pagecontacts.addQuestion();
                        }                    
                    }                
                });
            }
        },

        build: function()
        {

        },

        addQuestion: function()
        {
            var iCntQuestions = 0;
            $('.full_question_holder').each(function(){
                iCntQuestions++;
            });
            

            /*iCntQuestions = iCntQuestions - 1;*/
            
            if (iCntQuestions >= $Core.pagecontacts.aParams.iMaxQuestions)
            {
                
                return false;
            }
            
            $('#hiddenQuestion').find(':text').each(function(){
                $(this).val('');
            });

            $('#js_quiz_container').append('' + $('#hiddenQuestion').html() + '');
        
            $Core.pagecontacts.fixQuestionsIndexes();
            
            $('.full_question_holder:last').find('.hdnCorrectAnswer:first').val('1');
            $('.full_question_holder:last').find('.p_2:first').addClass('correctAnswer');            

            return false;
        },

        submitForm : function()
        {
            $('#js_quiz_layout_default').html('');
            return true;
        },

        fixQuestionsIndexes : function()
        {
            var iCntQuestions = 1;
            var topic_count = '<?php echo $sTopicCount; ?>';
            var oDate = new Date();

            $('#js_quiz_container').find('.full_question_holder').each(function(){
        
                var iCntAnswers = 0;

            
                $(this).find('.topic_title').attr('name', 'val[q][' + (iCntQuestions) + '][question]');
                $(this).find('.email').attr('name', 'val[q][' + (iCntQuestions) + '][email]');
            
                $(this).find('.answer_parent').each(function()
                {
                                
                    $(this).find('.answer').attr('name', 'val[q][' + (iCntQuestions) + '][answers]['+iCntAnswers+'][answer]');
                    $(this).find('.hdnCorrectAnswer').attr('name', 'val[q][' + iCntQuestions + '][answers][' + iCntAnswers + '][is_correct]');
                    $(this).find('.answer').attr('name', 'val[q]['+iCntQuestions+'][answers]['+iCntAnswers+'][answer]');
                    $(this).find('.hdnAnswerId').attr('name', 'val[q]['+iCntQuestions+'][answers]['+iCntAnswers+'][answer_id]');
                    $(this).find('.hdnQuestionId').attr('name', 'val[q]['+iCntQuestions+'][answers]['+iCntAnswers+'][question_id]');
                    if ($(this).find('.hdnQuestionId').val() == undefined)
                    {
                        $(this).find('.hdnQuestionId').val(iCntQuestions + iCntAnswers + '123321');
                    }
                    iCntAnswers++;
                });
                
                $(this).find('.question_title').attr('name', 'val[q]['+iCntQuestions+'][question]');
                
                if (iCntQuestions <= $Core.pagecontacts.aParams.iMinQuestions)
                {
                    $(this).find('.question_number_title').html($Core.pagecontacts.aParams.sRequired +topic_count.replace('{count}', ''));
                    $(this).find("#removeQuestion").show();
                }
                else
                {
                    $(this).find('.question_number_title').html(topic_count.replace('{count}', ''));    
                    $(this).find("#removeQuestion").show();
                }
                
                iCntQuestions++;
            }); 
            var tabIndex = 1;
            $('.full_question_holder').each(function() {
                $(':input',this).not('input[type=hidden]').each(function() {
                    if ($(this).attr('type') == 'text' || $(this).attr('type') == 'textarea')
                    {
                        $(this).attr('tabindex', tabIndex);
                        tabIndex++;
                    }
                });
            });
            
            
        },
        
        removeQuestion: function(oObj)
        {

            var iCntQuestions = 0;
            $('.full_question_holder').each(function(){
                iCntQuestions++;
            });
			
            iCntQuestions = iCntQuestions - 1;
            if (iCntQuestions == 2)
            {
            	$('#js_quiz_container').find('#removeQuestion').hide();
            }
            
            if (iCntQuestions <= $Core.pagecontacts.aParams.iMinQuestions)
            {
            //    return false;
            }
            $Core.pagecontacts.iTotalQuestions = iCntQuestions;

            $(oObj).parents('.full_question_holder:first').remove();

            return false;
        }
    }
</script>
<?php ?>