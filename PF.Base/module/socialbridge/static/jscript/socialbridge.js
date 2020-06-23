function openauthsocialbridge(pageURL){
        var w = 800;
        var h = 500;
        var title ="openauthsocialbridge";
        var left = (screen.width/2)-(w/2);
        var top = (screen.height/2)-(h/2);
        var newwindow = window.open (pageURL, title, 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
        if (window.focus) {newwindow.focus();}
        return newwindow;
};

