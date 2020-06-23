function appendPageOrders(){
    //alert($('#page2').html());
    var string = $('#page2').html().replace("<table>","");
    string = string.replace("</table>","");
    $('#tableOrders').append(string);
    $('#page2').remove();
}
