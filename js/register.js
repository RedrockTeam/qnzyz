/**
 * Created by andycall on 14-3-29.
 */


$(function(){
    $("#register").on('submit',function(e){


        var name = this.name.value,
            number = this.number.value,
            phone = this.phone.value;
        var Reg_number = /^[0-9]+$/;
        var Reg_phone = /^[0-9]{11}$/;

        if(!name){
            alert('骚年, 你的名字掉了!');
            return e.preventDefault();
        }
        if(!Reg_number.test(number)){
            alert("学号错啦!:(")
            return e.preventDefault();
        }
        else if(!Reg_phone.test(phone)){
            alert("骚年, 你确定你的手机号是这个: " + phone );
            return e.preventDefault();
        }


        return  e.returnValue = true;
    });
});