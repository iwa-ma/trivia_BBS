
 window.addEventListener('load',function(){
       // console.log("start!good_count_ini_function in!");
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var goodCountData;
      $(".wrapper [id^=good_]").each(function(i, o){
        var id_work = $(this).attr("id");
        if(localStorage.getItem('good_enable_' + id_work.substr(5) + '_' + io) == 'TRUE'){
          if (typeof(goodCountData) == "undefined"){
            goodCountData = [{No: id_work.substr(5)}];
          }else {
            var addData = {No: id_work.substr(5)};
            goodCountData.push(addData);
          }
        }
      });

      if(typeof(goodCountData) == "undefined"){
        goodCountData = {No: "no_data"};
      }
    var post_url = "index.php" + "?mode=" + io;
    goodCountData.page = "1";
    var aryJSON = JSON.stringify(goodCountData);
      $.ajax({
          type: "post",
          url: post_url,
          //data: { goodCountData : aryJSON }
          data: { goodCountData : aryJSON }
      })
      .then(
          function (data) {
           var contentsInnerHTML = $(data).filter('.wrapper').html();
           $('.wrapper').html(contentsInnerHTML);
             $.getScript("common/js/base.js", function(data, textStatus, jqxhr) {

              $('#newthread').css('pointer-events', 'auto');
              $('.inquiry').css('pointer-events', 'auto');
              $('.manual').css('pointer-events', 'auto');
              $('.operating_company').css('pointer-events', 'auto');
              $('.password_del').css('pointer-events', 'auto');
              $('.thr_update').css('pointer-events', 'auto');
              $('.thread-detail').css('pointer-events', 'auto');
              $('#category').css('pointer-events', 'auto');
              $('.search-button').css('pointer-events', 'auto');
              $('.searchform .input-group .search-query').css('pointer-events', 'auto');
            });
          },
          function () {
          });
      delete h_location;
  }, false);