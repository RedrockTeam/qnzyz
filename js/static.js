/**
 * Created by andycall on 14-3-18.
 */


jQuery(document).ready(function($){

    function PostData(URL,data,fn){
        var self = this;
        $.post(URL,data,function(e){
            fn.call(self,e);
        });
    }
    $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');// 这行是 Opera 的补丁, 少了它 Opera 是直接用跳的而且画面闪烁 by willin
    $(".lottery_button").find('a').on('click tap',function(e){
        if(parseInt($('#lottery_count').html()) <= 0){
            e.preventDefault();
            alert("你的抽奖机会已经用完啦!");
        }
    })

    $('.move_up').click(function(){
        $body.animate({scrollTop: 0}, 500);
        return false;// 返回false可以避免在原链接后加上#
    });
    var vote_success = $('.vote_success');
    var close = $('.close');
    close.click(function(){
        $(this).closest(".vote_success").hide(400);
        $(this).parent().find(".vote_wrapper").hide();
        $(this).parent().find(".vote_fail").hide();
        $(".sendButton").html("提交");
    });
    $(".sendButton").on('click tap',function(){
        vote.apply(this,arguments);
    });

    function vote(){
        var self = this;
        var URL = "./ajaxVote.php"; //ajax发送地址
        var sendStr = "",
            votes =[],
            voteds = $(".voted");

        voteds.each(function(index,value){
            var val = $(value).attr("data-troope");
            votes.push(val);
        });
        if(votes.length < 5){
            alert("投票的队伍必须为5个!");
            return;
        }

        for(var i = 0,len = votes.length; i <  len;  i ++){
            sendStr += votes[i];
            if(i < votes.length -1){
                sendStr += "&&";
            }
        }


        var data = {
            'teamStr':sendStr,
            'openId':$('#openId').val()
        };

        $(this).html("正在提交...");
        $(this).off("click tap");
        $('.vote').off('click tap');
        console.log('send');


        PostData.call(self,URL,data,afterSend,status);

        function afterSend(status){
            var state = parseInt(status);
            if(state == 0){
                $('#lottery_count').html(parseInt($('#lottery_count').html()) + 1);
                vote_success.show(400);
                vote_success.find(".vote_wrapper").css("display","block");
                $(".voted").find(".tickets").each(function(index,value){
                    voteCount = $(value).html() || 0;
                    $(value).html(parseInt(voteCount) + 1 + "票");
                });
                $(self).html("提交");

            }else{
                $(self).html("提交");
                if(state == -2 || state == -6){
                    vote_success.show(400);
                    vote_success.find(".vote_fail").css("display","block");
                }else{
                    switch(state){
                        case -1:
                            alert("投票数据非法");
                            break;
                        case -3:
                            alert("系统错误：修改票数失败");
                            break;
                        case -4:
                            alert("系统错误：新增投票记录失败");
                            break;
                        case -5:
                            alert("抱歉！活动还未开始");
                            break;
                        case -7:
                            alert("抱歉！活动已经结束");
                            break;
                        default:
                            alert("未知错误");
                            break;
                    }

                }
                
            }

//            console.log(1);
            $(self).on('click tap',function(){
                vote.apply(self,arguments);
            });
            $(".vote").on('click tap',function(e){
                voteHeart.call(this,e);
            })
            voteds.each(function(index,value){
                $(value).removeClass('voted');
                $(value).find(".vote").find("img").attr('src',$(value).find(".vote").find("img").attr('src').replace("heart","broken_heart"));
            });
        }
//        $.ajax(settings.ajaxURL,
//            {
//                str : sendStr
//            },afterSend);
//        $(".send").animate({"bottom": "-100px"});

    }

    function voteHeart(e){
        e.preventDefault();
        var count = $("#count").html();
        if(parseInt(count) > 0){
            alert("Sorry, 貌似活动尚未开放!");
            return false;
        }
        var troope = $(this).closest(".troopes");
        var votes = $('.voted').length || 0;
        var images = $(this).find("img");
        var voteCount = parseInt($(this).parent().find("span").html()) || 0;
        if(!troope.hasClass("voted")){
            if(votes < 5){
                images.attr('src',$(this).find("img").attr('src').replace("broken_heart","heart"));
                $(troope).addClass("voted");

            }
            else if(votes >= 5){
                alert("只能投五个队伍");
            }
        }
        else{
            $(troope).removeClass('voted');
            $(this).find("img").attr('src',$(this).find("img").attr('src').replace("heart","broken_heart"));
        }
    }




    $('.vote').on('click tap',function(e){
        voteHeart.call(this,e);
    });


});