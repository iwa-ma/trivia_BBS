function ua_type() {
    var ua = navigator.userAgent;
    if (ua.indexOf('iPhone') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0 || ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0) {
          return "sp";
    } else {
          return "pc";
    }
};

function io_type(io_work) {
      switch(io_work) {
        case "json":
          return "json";
          break;
        case "db":
          return  "db";
            break;
        default:
          return  "none";
      }
};

function good_count(){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var goodCountData;
      $("[id^=good_]").each(function(i, o){
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
    return goodCountData
};

function search_keycode() {
  if(event.keyCode == 13){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
     var search_text = $('#search_post').val();
     //全角スペースを半角スペースに
    search_text = search_text.replace(/　/g," ");
    //ここで半角スペース連続を1つにしたい。
    search_text = search_text.replace(/ {2,}/g," ");
    //前後の空白を削除
    search_text = search_text.trim();
    //半角スペース毎に分割
    search_text = search_text.split(" ");
    var url_search =$(location).attr('host');
    var url_search = 'http://'+url_search +'/bbs/'+ 'search.php';
    var search_data = {
     search_text : search_text,
     iotype : io,
     start_no :  1,
    };
      $.ajax({
          type: "post",
          url: "search.php",
          data: search_data,
      })
      .then(
          function (data) {
          $("#main").html(data);
           size_arr =w_size();
          $("#main").css("background-color",  "white");
          $('#search_post').val(" ");
          },
          function () {

          });
  }
};
 

function w_size(){
    ua_wsize = ua_type();
    var size_arr = {};
    size_arr["footer_hsize"] =  $('footer').height();//フッターの高さを取得
    size_arr["sidebarbox_hsize"] =  $("#sidebarbox").height();//サイドバーの高さを取得
    size_arr["main_hsize"] =  $("#main").height();//サイドバーの高さを取得

     // console.log("footer_hsize:"+size_arr["footer_hsize"]);
     // console.log("sidebarbox_hsize:"+size_arr["sidebarbox_hsize"]);
     // console.log("main_hsize:"+size_arr["main_hsize"]);

      if (size_arr["sidebarbox_hsize"]>size_arr["main_hsize"] && ua_wsize=="pc"){
        //sidebarboxの高さよりmainの高さが小さい、mainの高さ変更必要
          size_arr["result"] =  false;
          $("#main").css("margin-bottom",  "");
          $("#main").css("padding-bottom",  "");
          $("#contents").css("overflow", "");
          $("#main").css("padding-bottom",  "10000px");
          $("#main").css("margin-bottom",  "-10000px");
          $("#contents").css("overflow", "hidden");
      }else{
          size_arr["result"] =  true;
          $("#main").css("margin-bottom",  "");
          $("#main").css("padding-bottom",  "");
          $("#contents").css("overflow", "");
      }
     return size_arr;
}

function w_size_sp(){
    ua_wsize = ua_type();
    var size_arr = {};
    size_arr["wrapper_hsize"] =  $('.wrapper').height();
    size_arr["header_hsize"] =  $("#header").height();
    size_arr["contents_hsize"] =  $("#contents").height();
    size_arr["footer_hsize"] =  $("footer").height();
    size_arr["all"] = size_arr["header_hsize"]+size_arr["contents_hsize"]+size_arr["footer_hsize"];

    //mainの高さが小さい場合、余白を追加
    if(size_arr["wrapper_hsize"]>size_arr["all"]&& ua_wsize=="sp"){
/*      console.log("wrapper:"+size_arr["wrapper_hsize"]);
      console.log("header:"+size_arr["header_hsize"]);
      console.log("contents:"+size_arr["contents_hsize"]);
      console.log("footer:"+size_arr["footer_hsize"]);
      console.log("header+contents+footer:"+size_arr["all"]);
      console.log("サイズ調整必要");*/
      $("#main").css("margin-bottom",  "");
      $("#main").css("padding-bottom",  "");
      $("#main").css("padding-bottom",  "10000px");
      $("#main").css("margin-bottom",  "-10000px");
      $("#contents").css("overflow", "hidden");
    }else{
/*      console.log("wrapper:"+size_arr["wrapper_hsize"]);
      console.log("header:"+size_arr["header_hsize"]);
      console.log("contents:"+size_arr["contents_hsize"]);
      console.log("footer:"+size_arr["footer_hsize"]);
      console.log("header+contents+footer:"+size_arr["all"]);
      console.log("サイズ調整不要");*/
      $("#main").css("margin-bottom",  "");
      $("#main").css("padding-bottom",  "");
      $("#contents").css("overflow", "");
    }

     return size_arr;
}

function w_footer_ini(){
    if (ua_type() == "pc"){
      size_arr=w_size();
    }else{
      size_arr= w_size_sp();
    }
     main_result = $('#main').css("margin-bottom");
    //w_size_spで余白が追加されていない場合、footer分のみ確保する
    if(main_result!="0px"){
/*                console.log("footer 追加不要");*/
    } else {
      $("#main").css("margin-bottom",  size_arr["footer_hsize"] + "px");
/*                console.log("footer 追加必要");*/
    }
    $("#sidebarbox").css("margin-bottom",  size_arr["footer_hsize"] + "px");
}

$(function(){
  $(document).off('click');
  $("html,body").animate({scrollTop:$('html').offset().top});
 
//**footer用事前処理**//
      w_footer_ini();
//**footer事前処理**//

//**削除確認ダイアログ事前処理**//
    ua_dialog = ua_type();
    window_w = $(window).width();
    window_h = $(window).height();
    if(ua_dialog=="pc"){
      dialog_h = 200;
      dialog_w = 250;
    }else if(ua_dialog=="sp" && window_w <= 320){
      dialog_h = window_h *0.55;
      dialog_w = window_w *0.7;
    }else{
      dialog_h = 200;
      dialog_w = 250;
    }

     $( "#dialog" ).dialog({
       autoOpen: false,
       closeOnEscape: false,
        height: dialog_h,
         width: dialog_w,
        title: '削除確認',
        buttons:[
        {text: "削除する",click: function(){$( this ).dialog( "close" );}},
        {text: "削除しない",click: function(){$( this ).dialog( "close" );}}
        ]
      });
//**削除確認ダイアログ事前処理**//

$("input[name = title],input[name = penname],input[name = password] ").keypress(function (e) {
    if (e.which === 13) {
        return false;
    }
});
 

  $('.search-button').off('click');
  $('.search-button').on('click',function() {
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
     var search_text = $('#search_post').val();
    //全角スペースを半角スペースに
    search_text = search_text.replace(/　/g," ");
    //ここで半角スペース連続を1つにしたい。
    search_text = search_text.replace(/ {2,}/g," ");
    //前後の空白を削除
    search_text = search_text.trim();
    //半角スペース毎に分割
    search_text = search_text.split(" ");
    var url_search =$(location).attr('host');
    var url_search = 'http://'+url_search +'/bbs/'+ 'search.php';
    var search_data = {
     search_text : search_text,
     iotype : io,
     start_no :  1,
    };
      $.ajax({
          type: "post",
          url: "search.php",
          data: search_data,    
      })
      .then(
          function (data) {
            $("#main").html(data);
             size_arr =w_size();
            $("#main").css("background-color",  "white");
            $('#search_post').val(" ");
          },
          function () {

          });
  });

  $('#title_post').validationEngine('attach', {
      promptPosition:"bottomLeft"
    });

  //password_del.clickから実行。削除処理用のfunction
  function Del(pass_data_d) {
    $.post(
         "del_reg.php",
         pass_data_d,
         function(data){
             if (data.state == "DEL_END" ) {
                    $( "#dialog" ).dialog({
                        autoOpen: false,
                        closeOnEscape: false, 
                        title: '削除終了',
                        buttons:[
                      {text: "OK",click: function(){$( this ).dialog( "close" );
                        location.reload(true);
                      }}]
                     });
                    $('#dialog').text("削除処理が終了しました。");
                    $('#dialog').dialog('open'); 
              }else{

              }
         },"json"
      );
  };

  $('.password_del').off('click');
  $(document).on('click','.password_del',function() {
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());

    var pass_data = {
      password : $("[name='password_"+$(this).attr('thr_no')+"_"+$(this).attr('pos_no')+"']").val(),
      cat_name : $(this).attr('cat_name'),
      thr_no : $(this).attr('thr_no'),
      pos_no : $(this).attr('pos_no'),
      del_confirmation: 0,
      admin : $(this).attr('admin'),
      iotype : io,
    };

   if(pass_data.admin=="true"){
    $( "#dialog" ).dialog({
      autoOpen: false,
      closeOnEscape: false, 
      title: '削除確認',
        buttons:[
        {text: "削除する",click: function(){$( this ).dialog( "close" );
        //pass_dataに値追加して、del_confirmation=1にする
        pass_data.del_confirmation = 1;
        Del(pass_data);
              return false; 
        }},
        {text: "削除しない",click: function(){$( this ).dialog( "close" );
            return false;}
        }]
      });
      $('#dialog').text("管理者モードです。認証無く削除できます。削除しますか？");
      $('#dialog').dialog('open'); 
   }else{
    $.ajax({
      type: "post",
      url: "del_reg.php",
      data: pass_data,
      dataType:  "json",
    })
    .then(
      function (data) {
      //del_reg.phpからのレスポンスに対してパスワード認証完了
        if (data.state == "OK" ) {
        $( "#dialog" ).dialog({
            autoOpen: false,
            closeOnEscape: false, 
            title: '削除確認',
            buttons:[
          {text: "削除する",click: function(){$( this ).dialog( "close" );
            pass_data.del_confirmation = 1;
            Del(pass_data);
          }},
          {text: "削除しない",click: function(){$( this ).dialog( "close" );}
          }]
         });
        $('#dialog').text("パスワードの認証が完了しました。削除しますか？");
        $('#dialog').dialog('open'); 
       }else if(data.state == "NG" ){
        $( "#dialog" ).dialog({
            autoOpen: false,
            closeOnEscape: false, 
            title: '削除確認',
            buttons:[
            {text: "閉じる",click: function(){$( this ).dialog( "close" );}
          }]
         });
        $('#dialog').text("パスワードが間違っています。確認してください。");
        $('#dialog').dialog('open'); 
       }else if(data.state == "BLANK" ){
        $( "#dialog" ).dialog({
            autoOpen: false,
            closeOnEscape: false, 
            title: '削除確認',
           buttons:[
            {text: "閉じる",click: function(){$( this ).dialog( "close" );}
          }]
         });
        $('#dialog').text("パスワードを入力して下さい。");
        $('#dialog').dialog('open'); 
       }else if(data.state == "OK_DB" ){

       }else{

       }
      },
        function(XMLHttpRequest, textStatus, errorThrown)
      {

      });
   }//ifのend
  return false;
  });
  $('.good_count_plus').off('click');
  $('.posted').on('click','.good_count_plus',function(){
    var page_location = $(this).attr('location');
    var page_no = $(this).attr('page');
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var data_set = {
      cat_no : $(this).attr('cat_no'),
      thr_no : $(this).attr('thr_no'),
      pos_no : $(this).attr('pos_no'),
      iotype : io,
      page : page_no,
      location : "count_plus",
    };
    var target_name = data_set["cat_no"]+'_'+data_set["thr_no"]+'_'+data_set["pos_no"]+'_'+data_set["iotype"] ;
    var count = localStorage['good_enable_'+ data_set["cat_no"]+'_'+data_set["thr_no"]+'_'+data_set["pos_no"]+'_'+data_set["iotype"] ];
    if (count== "TRUE") {

    } else {
      var url_count =$(location).attr('host');
      var thr_no_count = '#'+$(this).parents("span").attr("id");
      var par =$(location).attr('search');

      switch(page_location){
        case "index":
          if(par.match(/mode=json/)){ 
          var url2_count = 'http://'+url_count +'/bbs/'+ 'index.php?mode=json&page='+ page_no +' ' + thr_no_count;
          }else if(par.match(/mode=db/)){
          var url2_count = 'http://'+url_count +'/bbs/'+ 'index.php?mode=db&page='+ page_no +' ' + thr_no_count;
          }
         break;
        case "detail":
          if(par.match(/mode=json/)){ 
          var url2_count = 'http://'+url_count +'/bbs/'+ 'thread-detail.php?mode=json&page='+ page_no+ thr_no_count;
          }else if(par.match(/mode=db/)){
          var url2_count = 'http://'+url_count +'/bbs/'+ 'thread-detail.php?mode=db&page='+ page_no+ thr_no_count;
          }
         break;
      }
        $.ajax({
            type: "post",
            url: "good_count_reg.php",
            data: data_set,
            dataType: "json",})
          .then(
          function (data,textStatus,jqXHR) {
            if (data.state == "OK"){
            localStorage['good_enable_' + target_name] = "TRUE";

                  if(ua_type()==="pc"){
                    var addData = good_count();
                    data_set['goodCountData'] = addData;
                  }
             switch(page_location){

                case "index":
                    $.ajax({
                      type: "post",
                      url: url2_count,
                      data: data_set,
                      dataType: 'html',})
                    .then(
                    function (data,textStatus,jqXHR) {
                      $(thr_no_count).html($(data).find(thr_no_count)[0].children[0]);
                    },
                    function () {
                      alert("読み込み失敗");
                    });

                 break;

                case "detail":
                     if(ua_type()==="sp"){
                        var addData = good_count();
                        data_set['goodCountData'] = addData;
                     }
                    $.ajax({
                      type: "post",
                      url: url2_count,
                      data: data_set,
                      dataType: 'html',})
                    .then(
                    function (data,textStatus,jqXHR) {
                      $(thr_no_count).html($(data).find(thr_no_count)[0].children[0]);
                    },
                    function () {
                      alert("読み込み失敗");
                    });
                break;
              }

            };
          },
          function (jqXHR, textStatus, errorThrown) {

          });
    }// else end
    count = "";
  });

  $(".thr_update").off('click');
  $(".thr_update").on('click',function(){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var url_update_detail =$(location).attr('host');
    var par =$(location).attr('search');
      var update_detail_data = {
      cat_no : $(this).attr('cat_no'),
      cat_name : $(this).attr('cat_name'),
      thr_no : $(this).attr('thr_no'),
      iotype : io,
      page : "1",
       };
        update_detail_data['goodCountData'] = "no_data";
      $.ajax({
          type: "post",
          url: "thread-detail.php",
          data: update_detail_data,
          success: function(data, dataType)
          {
          $("#main").html(data)
           size_arr =w_size();
          $("#main").css("background-color",  "white");
          $("html,body").animate({scrollTop:$('html').offset().top});
          },
         //Ajax通信が失敗した場合のメッセージ
          error: function()
          {
          }
      });

  });
  $('#newthread').off('click');
  $("#newthread").on('click',function(){
    var par =$(location).attr('search');
    var url_menu =$(location).attr('host');
    if(par.match(/mode=json/)){
      var url2_menu = 'http://'+url_menu +'/bbs/'+ 'threadform.php?mode=json';
    }else if(par.match(/mode=db/)){
      var url2_menu = 'http://'+url_menu +'/bbs/'+ 'threadform.php?mode=db';
    }
        $("#main").load(url2_menu,function(response, status, xhr) { 
         size_arr =w_size();
         $("#main").css("background-color",  "white");
         });
  });

  $("#category").off('click');
  $("#category").on('click','.catlist',function(){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var　data = {
      cat_name : $(this).attr('cat_name'),
      cat_no : $(this).attr('cat_no'),
      start_no : $(this).attr('start_no'),
      thr_count_all : $(this).attr('thr_count_all'),
      iotype : io,
      dataType:  "json",
    };
     $.ajax({
          type: "post",
          url: "threadlist.php",
          data: data,})
        .then(
        function (data) {
          $("#main").html(data);
           size_arr =w_size();
          $("#main").css("background-color",  "white");
          $('.thread-detail').css('pointer-events', 'auto');
        },
        function () {
          alert("読み込み失敗");
        });
  });

  $('.thread-detail').off('click');
  $(document).on('click','.thread-detail',function() {
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
      var detail_data = {
      cat_name : $(this).attr('cat_name'),
      thr_no : $(this).attr('thr_no'),
      iotype : io,
      page : "1",
     };
        detail_data['goodCountData'] = "no_data";
        $.ajax({
            type: "post",
            url: "thread-detail.php",
            data: detail_data,})
          .then(
          function (data) {
            $("#main").html(data);
             size_arr =w_size();
            $("#main").css("background-color",  "white");
            $("html,body").animate({scrollTop:$('html').offset().top});
          },
          function () {
            alert("読み込み失敗");
          });
   });
  $('.paging .pager-back').off('click');
  $(document).on('click','.paging .pager-back',function(){
    var page_location = $(this).attr('location');
    var page_no_back = $(this).attr('page');
    var url_back =$(location).attr('host');
    var par =$(location).attr('search');
    var pagination_no_back = '#'+$(this).parents("span").attr("id");

    switch(page_location){
      case "index":
        if(par.match(/mode=json/)){
          var url_after_back = 'http://'+url_back +'/bbs/'+ 'index.php?mode=json&page='+ page_no_back +' ' + pagination_no_back;
        }else if(par.match(/mode=db/)){
          var url_after_back = 'http://'+url_back +'/bbs/'+ 'index.php?mode=db&page='+ page_no_back +' ' + pagination_no_back;
        }
        $(pagination_no_back).load(url_after_back);
        $("html,body").animate({scrollTop:$(pagination_no_back).offset().top});
      break;

      case "detail":
          var io_work = $(location).attr('search').substr(6);
          io = io_type(io_work.toLowerCase());
          var detail_data = {
            cat_name : $(this).attr('cat_name'),
            thr_no : $(this).attr('thr_no'),
            pos_id : $(this).attr('pos_id'),
            page: $(this).attr('page'),
            iotype : io,
            next_no :  $(this).attr('next_no'),
            pagetype : "back",
          };
          detail_data['goodCountData'] = "no_data";
          $.ajax({
            type: "post",
            url: "thread-detail.php",
            data: detail_data,})
          .then(
          function (data) {
              $("#main").html(data);
               size_arr =w_size();
          },
          function () {
            alert("読み込み失敗");
          });
          break;

      case "thread-list":
          var io_work = $(location).attr('search').substr(6);
          io = io_type(io_work.toLowerCase());
          var data = {
            cat_no : $(this).attr('cat_no'),
            page: $(this).attr('page'),
            iotype : io,
            next_no :  $(this).attr('next_no'),
            start_no :  $(this).attr('start_no'),
            thr_count_all :  $(this).attr('thr_count_all'),
            pagetype : "back",
          };
          $.ajax({
            type: "post",
            url: "threadlist.php",
            data: data,})
          .then(
          function (data) {
              $("#main").html(data);
               size_arr =w_size();
               $('.thread-detail').css('pointer-events', 'auto');
          },
          function () {
            alert("読み込み失敗");
          });
          break;

      case "search":
        var data = {
          search_text : $(this).attr('search_text'),
          page :  $(this).attr('page'),
          next_no :  $(this).attr('next_no'),
          start_no :  $(this).attr('start_no'),
          iotype : io,
          pagetype : "back",
          };
            $.ajax({
                type: "post",
                url: "search.php",
                data: data,
                //Ajax通信が成功した場合
                success: function(data, dataType)
                {
                 $("#main").html(data);
                 size_arr =w_size();
                 $('.thread-detail').css('pointer-events', 'auto');
                },
                error: function()
                {
                }
            });
        break;
    }
  });

  $('.paging .pager-next').off('click');
  $(document).on('click','.paging .pager-next',function(){
    var page_location = $(this).attr('location');
    var page_no = $(this).attr('page');
    var url  = $(location).attr('host');
    var par  = $(location).attr('search');
    var path = $(location).attr('pathname');
    var pagination_no = '#'+$(this).parents("span").attr("id");

    switch(page_location){
      case "index":
        if(par.match(/mode=json/)){
          var url_after = 'http://'+url +'/bbs/'+ 'index.php?mode=json&page='+ page_no +' '+pagination_no;
        }else if(par.match(/mode=db/)){
          var url_after = 'http://'+url +'/bbs/'+ 'index.php?mode=db&page='+ page_no +' '+pagination_no;
        }
        $(pagination_no).load(url_after);
        $("html,body").animate({scrollTop:$(pagination_no).offset().top});
      break;

      case "detail":
        var io_work = $(location).attr('search').substr(6);
        io = io_type(io_work.toLowerCase());
        var detail_data = {
          cat_name : $(this).attr('cat_name'),
          thr_no : $(this).attr('thr_no'),
          pos_id : $(this).attr('pos_id'),
          page: $(this).attr('page'),
          iotype : io,
          next_no :  $(this).attr('next_no'),
          pagetype : "next",
        };
        detail_data['goodCountData'] = "no_data";
        $.ajax({
            type: "post",
            url: "thread-detail.php",
            data: detail_data,})
          .then(
          function (data) {
               $("#main").html(data);
               size_arr =w_size();
          },
          function () {
            alert("読み込み失敗");
          });
        break;

      case "thread-list":
          var io_work = $(location).attr('search').substr(6);
          io = io_type(io_work.toLowerCase());
          var data = {
            cat_no : $(this).attr('cat_no'),
            page: $(this).attr('page'),
            iotype : io,
            next_no : $(this).attr('next_no'),
            start_no :  $(this).attr('start_no'),
            thr_count_all :  $(this).attr('thr_count_all'),
            pagetype : "next",
          };
          $.ajax({
            type: "post",
            url: "threadlist.php",
            data: data,})
          .then(
          function (data) {
              $("#main").html(data);
               size_arr =w_size();
               $('.thread-detail').css('pointer-events', 'auto');
          },
          function () {
            alert("読み込み失敗");
          });
          break;

      case "search":
       var data = {
        search_text : $(this).attr('search_text'),
        page :  $(this).attr('page'),
        next_no :  $(this).attr('next_no'),
        start_no :  $(this).attr('start_no'),
        pagetype : "next",
        iotype : io,};
          $.ajax({
              type: "post",
              url: "search.php",
              data: data,
              //Ajax通信が成功した場合
              success: function(data, dataType)
              {
              $("#main").html(data);
               size_arr =w_size();
               $('.thread-detail').css('pointer-events', 'auto');
              },
             //Ajax通信が失敗した場合のメッセージ
              error: function()
              {

              }
          });
        break;
    }
  });

  $('.newpost').off('click');
  $(".newpost").on('click',function(){
    var page_no = $(this).attr('page');
    var url =$(location).attr('host');
    var par =$(location).attr('search');
    if(par.match(/mode=json/)){
      var url_after = 'http://'+url +'/bbs/'+ 'index.php?mode=json';
    }else if(par.match(/mode=db/)){
      var url_after = 'http://'+url +'/bbs/'+ 'index.php?mode=db';
    }
    location.href = url_after;
  });
  $('.search_sp').off('click');
  $(".search_sp").on('click',function(){
    var url =$(location).attr('host');
    var par =$(location).attr('search');
    if(par.match(/mode=json/)){
      var url_after = 'http://'+url +'/bbs/temp/'+ 'search_top_SP.php?mode=json';
    }else if(par.match(/mode=db/)){
      var url_after = 'http://'+url +'/bbs/temp/'+ 'search_top_SP.php?mode=db';
    }
      $("#main").load(url_after,function(response, status, xhr) { 
       size_arr =w_size_sp();
      $("#main").css("background-color",  "white");
      $('.search-button').css('pointer-events', 'auto');
      $('.searchform .input-group .search-query').css('pointer-events', 'auto');
     });
  });
  $('.newthrpost').off('click');
  $(".newthrpost").on('click',function(){
    var url =$(location).attr('host');
    var par =$(location).attr('search');
    if(par.match(/mode=json/)){
      var url_after = 'http://'+url +'/bbs/'+ 'threadform.php?mode=json';
    }else if(par.match(/mode=db/)){
      var url_after = 'http://'+url +'/bbs/'+ 'threadform.php?mode=db';
    }
    $("#main").load(url_after,function(response, status, xhr) { 
       size_arr =w_size();
       $("#main").css("background-color",  "white");
     });
  });

  $('.manual').off('click');
  $(".manual").on('click',function(){
      var url =$(location).attr('host');
      var url = 'http://'+url +'/bbs/'+ 'manual.php';
      $("#main").load(url,function(response, status, xhr) { 
      w_footer_ini();
      $("#main").css("background-color",  "#bafec94d");
      $("html,body").animate({scrollTop:0});
      });

  });
  $('.operating_company').off('click');
  $(".operating_company").on('click',function(){
      var url =$(location).attr('host');
      var url = 'http://'+url +'/bbs/'+ 'company_info.php';
      $("#main").load(url,function(response, status, xhr) { 
      if (ua_type() == "pc"){
        size_arr=w_size();
      }else{
        size_arr= w_size_sp();
      }
      $("#main").css("background-color",  "#bafec94d");
      $("html,body").animate({scrollTop:0});
      });
  });

  $('.inquiry').off('click');
  $(".inquiry").on('click',function(){
      var url =$(location).attr('host');
      var url = 'http://'+url +'/bbs/'+ 'Inquiryform.php';
      $("#main").load(url,function(response, status, xhr) { 
      if (ua_type() == "pc"){
        size_arr=w_size();
      }else{
        size_arr= w_size_sp();
      }
      $("#main").css("background-color",  "#bafec94d");
      $("html,body").animate({scrollTop:0});
      });
  });

  $('#thread').submit(function() {
   if(jQuery("#thread").validationEngine('validate')){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    
    if($('#ip_type').text()== "add"){
      $("#category_con") . text($('#category_post').text());
      $("#category_con_h") .val($('#category_post').text()); 
    }else{
      $("#category_con") . text($('#category_post').val());
      $("#category_con_h") .val($('#category_post').val());
    }

    $("#thread_title_con") . text($('#thread_title').text() );
    $("#thr_id_h") . val($('#thr_id').text());
    $("#title_con") . text($('#title_post').val() );
    $("#title_con_h") . val($('#title_post').val() );

    $("#maintext_con") . text($('#maintext_post').val() );
    $("#maintext_con_h") . val($('#maintext_post').val() );
     if($('#penname_post').val().replace(/^\s+/g, "").length == 0){
      $("#penname_con") . text("名無し");
      $("#penname_con_h") . val("名無し");
       }else{
      $("#penname_con") . text($('#penname_post').val() );
      $("#penname_con_h") . val($('#penname_post').val() );
      }

     if($('#password_post').val().replace(/^\s+/g, "").length == 0){
      $("#password_con")  . text("設定なし");
      $("#password_con_h") . val($('#password_post').val() );
     }else{
      $("#password_con") . text("設定あり");
      $("#password_con_h") . val($('#password_post').val() );
      }

    $(".thread .thread-item-root").css("display", "none");
    $(".posted-container").css("display", "none");
    $(".threadform").css("display", "none");
    $(".paging").css("display", "none");
    $(".confirmation_form").css("display", "block");
    $("html,body").animate({scrollTop:0});
      if (ua_type() == "pc"){
        size_arr=w_size();
      }else{
        size_arr= w_size_sp();
      }
      w_footer_ini();
        return false;
      }
  });

  $('.New_Confirmation_back').click(function() {
    $(".threadform").css("display", "block");
    $(".confirmation_form").css("display", "none");
      $("html,body").animate({scrollTop:0});
      if (ua_type() == "pc"){
        size_arr=w_size();
      }else{
        size_arr= w_size_sp();
      }
      w_footer_ini();
  });


  $('.Confirmation_back').click(function() {
    $ua = ua_type();
    if($ua=="pc"){
      $(".thread .thread-item-root").css("display", "flex");
    }else{
      $(".thread .thread-item-root").css("display", "block");
    }
    $(".posted-container").css("display", "flex");
    $(".threadform").css("display", "block");
    $(".paging").css("display", "block");
    $(".confirmation_form").css("display", "none");
    $("html,body").animate({scrollTop:$('#thread').offset().top});
      if (ua_type() == "pc"){
        size_arr=w_size();
      }else{
        size_arr= w_size_sp();
      }
      w_footer_ini();
  });

  $('.page_top_scroll').click(function() {
     $("html,body").animate({scrollTop:0});
  });


  $('#posted').click(function() {
         var threadform = '.threadform';
     $("html,body").animate({scrollTop:$(threadform).offset().top});
  });

  $('#menu').off('click');

  $('.thread-detail_search').off('click');
   $('.thread-detail_search').click(function() {
      var detail_data = {
      cat_no : $(this).attr('cat_no'),
      thr_no : $(this).attr('thr_no'),
      iotype : $(this).attr('iotype'),
      page : "1",
     };
        detail_data['goodCountData'] = "no_data";
              $.ajax({
                  type: "post",
                  url: "thread-detail.php",
                  data: detail_data,
                  //Ajax通信が成功した場合
                  success: function(data, dataType)
                  {
                  $("#main").html(data);
                  size_arr =w_size();
                  $("#main").css("background-color",  "white");
                  $("html,body").animate({scrollTop:$('html').offset().top});
                  },
                 //Ajax通信が失敗した場合のメッセージ
                  error: function()
                  {
                  }
              });
   });

  $('.category-item').off('click');
  $(".category-item").on('click',function(){
    var io_work = $(location).attr('search').substr(6);
    io = io_type(io_work.toLowerCase());
    var　data = {
      cat_name : $(this).attr('cat_name'),
      cat_no : $(this).attr('cat_no'),
      start_no : 1,
      thr_count_all : $(this).attr('thr_count_all'),
      iotype : io,
    };
     $.ajax({
          type: "post",
          url: "threadlist.php",
          data: data,})
        .then(
        function (data) {
        $("#main").html(data);
       size_arr =w_size_sp();
        },
        function () {
          alert("読み込み失敗");
        });
  });

});

jQuery(document).ready(function(){
  jQuery("#thread").validationEngine();
});