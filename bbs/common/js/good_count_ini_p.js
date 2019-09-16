$(function(){
   var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());

    var goodCountData;
      $("[id^=good_]").each(function(i, o){
        var id_work = $(this).attr("id");

        if(localStorage.getItem('good_enable_' + id_work.substr(5) + '_' + io) == 'TRUE'){
          if (typeof(goodCountData) == "undefined"){          
             goodCountData = {No: id_work.substr(5)};

          }else {
            var count = Object.keys(goodCountData).length;
            var No = 'No' + count+1;
            goodCountData[No] = id_work.substr(5);
          }
        }
      });

      if(typeof(goodCountData) == "undefined"){
        goodCountData = { No : "no_data"};
        goodCountData.iotype = io;
        var h_cat_no = document.getElementById('h_cat_no');
        goodCountData.cat_no = h_cat_no.textContent;

        var h_cat_name = document.getElementById('h_cat_name');
        goodCountData.cat_name = h_cat_name.textContent;

        var h_thr_no = document.getElementById('h_thr_no');
        goodCountData.thr_no = h_thr_no.textContent;

        var h_page = document.getElementById('h_page');
        goodCountData.page = h_page.textContent;

        var h_next_no = document.getElementById('h_next_no');
        goodCountData.next_no = h_next_no.textContent;

        goodCountData.goodCountData = "NULL";

        var h_pagetype = document.getElementById('h_pagetype');
        goodCountData.pagetype = h_pagetype.textContent;
      }else{
        goodCountData.iotype = io;
        var h_cat_no = document.getElementById('h_cat_no');
        goodCountData.cat_no = h_cat_no.textContent;

        var h_cat_name = document.getElementById('h_cat_name');
        goodCountData.cat_name = h_cat_name.textContent;

        var h_thr_no = document.getElementById('h_thr_no');
        goodCountData.thr_no = h_thr_no.textContent;

        var h_page = document.getElementById('h_page');
        goodCountData.page = h_page.textContent;
        goodCountData.goodCountData = "NULL";

        var h_next_no = document.getElementById('h_next_no');
        goodCountData.next_no = h_next_no.textContent;

        var h_pagetype = document.getElementById('h_pagetype');
        goodCountData.pagetype = h_pagetype.textContent;
      }
       var post_url = "thread-detail.php" + "?mode=" + io;
       var aryJSON = JSON.stringify(goodCountData);

      $.ajax({
          type: "post",
          url: post_url,
         data: goodCountData
      })
      .then(
          function (data) {
           var contentsInnerHTML = $(data).filter('#contents').html();
           $('#main').html(contentsInnerHTML);
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
});