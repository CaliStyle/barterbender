function yncEditorClick()
{
    if ((!getParam('bWysiwyg') || typeof(bForceDefaultEditor) != 'undefined') && typeof(Editor) == 'object'){
        //  
        Editor.setId('skills');
        Editor.getEditors();
        
        $("#description").click(function() {
            Editor.setId('description');
        });
        
        $("#js_editor_menu_description").bind("click", function(){
            Editor.setId("description");
        });
        $("#layer_description").bind("click", function(){
            Editor.setId("description");
        });
        $("#js_editor_menu_skills").bind("click", function(){
            Editor.setId("skills");
        });
        $("#layer_skills").bind("click", function(){
            Editor.setId("skills");
        });
        $("a.js_hover_title").bind("click", function() {
            Editor.setId('description');
        });             
    } else {
        if($('#js_editor_menu_description').length > 0){
            $('#js_editor_menu_description').hide();
        }
    }	
} 


$Behavior.yncInitAdd = function()
{
	yncEditorClick();
}
