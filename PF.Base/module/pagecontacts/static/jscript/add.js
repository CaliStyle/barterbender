$Behavior.contactAddQuestionClick = function()
{
	$Core.pagecontacts.init({sRequired:"*", isAdd: true, bErrors: true, iMaxAnswers: 3, iMinAnswers: 0, iMaxQuestions: 5, iMinQuestions: 1});
	$('#js_add_question').click(function()
	{
		$Core.pagecontacts.addQuestion();
		return false;
	});
	console.log('go here');
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
		

		iCntQuestions = iCntQuestions - 1;
		if (iCntQuestions >= $Core.pagecontacts.aParams.iMaxQuestions)
		{
			alert(oTranslations['quiz.you_have_reached_the_maximum_questions_allowed_per_quiz']);
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
				$(this).find('.question_number_title').html($Core.pagecontacts.aParams.sRequired + oTranslations['quiz.question_count'].replace('{count}', iCntQuestions));
			}
			else
			{
				$(this).find('.question_number_title').html(oTranslations['quiz.question_count'].replace('{count}', iCntQuestions));				
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
		if (iCntQuestions <= $Core.pagecontacts.aParams.iMinQuestions)
		{
			alert(oTranslations['quiz.you_are_required_a_minimum_of_total_questions'].replace('{total}', $Core.pagecontacts.aParams.iMinQuestions));
			return false;
		}
		$Core.pagecontacts.iTotalQuestions = iCntQuestions;

		$(oObj).parents('.full_question_holder:first').remove();

		return false;
	}

}

