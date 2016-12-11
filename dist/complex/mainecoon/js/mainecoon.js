window.DirList = [];
window.MaineCoonUrl = "/mainecoon.php";
window.MaineCoonMethod = "POST";

$(document).ready(function(){

    setControls();

    $('#action-form-1').submit(function(event){
        event.preventDefault();

        var jqxhr = $.ajax({
                type: window.MaineCoonMethod,
                url: window.MaineCoonUrl,
                data: { action: "snapshot" }
            })
            .done(function(json) {
                var response = $.parseJSON(json);
                window.DirList = response.data;
                window.DirListCount = response.count;
                $('.dirlist').append(response.html);
                parseDirList();
                $('#action-form-1').hide();
                $('#action-form-3').show();

            })
            .fail(function() {
                //alert( "error" );
            })
            .always(function() {
                //alert( "complete" );
            });
    });
});

function parseDirList()
{
    var DirList = window.DirList;
    var count = window.DirListCount;
    //console.log(count);
    var counter = 0;
    var data;

    for(var hash in DirList) {
        if (DirList.hasOwnProperty(hash)) {
            var attr = DirList[hash];
            data = parseDir(hash);
            drawProgressBar(counter, count, '');
            counter++;
        }
    }

    drawProgressBar(count, count); // 100% прогресса
    makeListMinimizable();

/*    DirList.forEach(function(dir, hash, DirList) {
        console.log(dir);
        console.log(hash);
        console.log(DirList);
        parseDir(hash)
    });*/
}

function parseDir(hash)
{
    var jqxhr = $.ajax({
            type: window.MaineCoonMethod,
            url: window.MaineCoonUrl,
            async: false,
            data: {
                action: "snapshot",
                folder: hash
            }
        })
        .done(function(json) {

            var response = $.parseJSON(json);

            $('#folder-'+hash+' .status').append(response.status);
            $('#folder-'+hash+' .folder-files').append(response.html);
            return response;
            /*
            console.log(data);
            window.DirList = data.data;
            $('.dirlist').append(data.html);
            parseDirList();*/
        })
        .fail(function() {
            //alert( "error" );
        })
        .always(function() {
            //alert( "complete" );
        });
}


function drawProgressBar(counter, count, className)
{
    //console.log('counter = ' + counter);
    //console.log('count = ' + count);

    var $progressBar = $('#progress-bar');
    //var $progressBar = document.getElementById("progress-bar");
    var current = parseInt((100 * counter) / count);

    //console.log(current);

    $progressBar.css("width", current+"%");

    if (className != '')
    {
        $progressBar.addClass(className);
    }
}

function makeListMinimizable()
{
    /*
     *  Клик по заоловку сворачивает или развочивает список файло
     */
    $('.folder-title').click(function(){
        $(this).find('.arrow').toggleClass('down');
        $(this).parent().find('.folder-files').toggleClass('minimized');
    });
}

function setControls()
{
    $('.control--toggle-all').click(function(){

        var minimized = $(this).data('minimized');

        if (minimized == 'minimized')
        {
            $('.folder-files').removeClass('minimized');
            $('.folder-title .arrow').removeClass('down');
            $(this).data('minimized', '');
        }
        else
        {
            $('.folder-files').addClass('minimized');
            $('.folder-title .arrow').addClass('down');
            $(this).data('minimized', 'minimized');
        }
    });
}