(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-login"],{8012:function(t,i,n){"use strict";var e=n("fc3a"),o=n.n(e);o.a},"9e30":function(t,i,n){"use strict";n.r(i);var e=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticClass:"input-group"},[n("v-uni-view",{staticClass:"input-row border"},[n("v-uni-text",{staticClass:"title"},[t._v("账号：")]),n("v-uni-input",{attrs:{type:"text",placeholder:"请输入账号"},model:{value:t.account,callback:function(i){t.account=i},expression:"account"}})],1),n("v-uni-view",{staticClass:"input-row"},[n("v-uni-text",{staticClass:"title"},[t._v("密码：")]),n("v-uni-input",{attrs:{type:"text",password:"true",placeholder:"请输入密码"},model:{value:t.password,callback:function(i){t.password=i},expression:"password"}})],1)],1),n("v-uni-view",{staticClass:"btn-row"},[n("v-uni-button",{staticClass:"primary",attrs:{type:"primary"},on:{click:function(i){i=t.$handleEvent(i),t.bindLogin(i)}}},[t._v("登录")])],1),n("v-uni-view",{staticClass:"action-row"},[n("v-uni-navigator",{attrs:{url:"../reg/reg"}},[t._v("注册账号")]),n("v-uni-text",[t._v("|")]),n("v-uni-navigator",{attrs:{url:"../pwd/pwd"}},[t._v("忘记密码")])],1),t.hasProvider?n("v-uni-view",{staticClass:"oauth-row",style:{top:t.positionTop+"px"}},t._l(t.providerList,function(i){return n("v-uni-view",{key:i.value,staticClass:"oauth-image"},[n("v-uni-image",{attrs:{src:i.image},on:{click:function(n){n=t.$handleEvent(n),t.oauth(i.value)}}})],1)})):t._e()],1)},o=[],a=n("d859"),r=n("2f62");function s(t){for(var i=1;i<arguments.length;i++){var n=null!=arguments[i]?arguments[i]:{},e=Object.keys(n);"function"===typeof Object.getOwnPropertySymbols&&(e=e.concat(Object.getOwnPropertySymbols(n).filter(function(t){return Object.getOwnPropertyDescriptor(n,t).enumerable}))),e.forEach(function(i){c(t,i,n[i])})}return t}function c(t,i,n){return i in t?Object.defineProperty(t,i,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[i]=n,t}var u={data:function(){return{providerList:[],hasProvider:!1,account:"",password:"",positionTop:0}},computed:Object(r["c"])(["forcedLogin"]),methods:s({},Object(r["b"])(["login"]),{initProvider:function(){var t=this,i=["weixin","qq","sinaweibo"];uni.getProvider({service:"oauth",success:function(n){if(n.provider&&n.provider.length){for(var e=0;e<n.provider.length;e++)~i.indexOf(n.provider[e])&&t.providerList.push({value:n.provider[e],image:"../../static/img/"+n.provider[e]+".png"});t.hasProvider=!0}},fail:function(t){console.error("获取服务供应商失败："+JSON.stringify(t))}})},initPosition:function(){this.positionTop=uni.getSystemInfoSync().windowHeight-100},bindLogin:function(){if(this.account.length<5)uni.showToast({icon:"none",title:"账号最短为 5 个字符"});else if(this.password.length<6)uni.showToast({icon:"none",title:"密码最短为 6 个字符"});else{var t={account:this.account,password:this.password},i=a["a"].getUsers().some(function(i){return t.account===i.account&&t.password===i.password});i?this.toMain(this.account):uni.showToast({icon:"none",title:"用户账号或密码不正确"})}},oauth:function(t){var i=this;uni.login({provider:t,success:function(n){uni.getUserInfo({provider:t,success:function(t){i.toMain(t.userInfo.nickName)}})},fail:function(t){console.error("授权登录失败："+JSON.stringify(t))}})},toMain:function(t){this.login(t),this.forcedLogin?uni.reLaunch({url:"../main/main"}):uni.navigateBack()}}),onLoad:function(){this.initPosition(),this.initProvider()}},l=u,p=(n("8012"),n("2877")),d=Object(p["a"])(l,e,o,!1,null,"79abbfea",null);d.options.__file="login.vue";i["default"]=d.exports},d859:function(t,i,n){"use strict";var e="USERS_KEY",o=function(){var t="";return t=uni.getStorageSync(e),t||(t="[]"),JSON.parse(t)},a=function(t){var i=o();i.push({account:t.account,password:t.password}),uni.setStorageSync(e,JSON.stringify(i))};i["a"]={getUsers:o,addUser:a}},fc3a:function(t,i,n){var e=n("fe14");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var o=n("4f06").default;o("493950bc",e,!0,{sourceMap:!1,shadowMode:!1})},fe14:function(t,i,n){i=t.exports=n("2350")(!1),i.push([t.i,"\n.action-row[data-v-79abbfea]{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center\n}\n.action-row uni-navigator[data-v-79abbfea]{color:#007aff;padding:0 20px\n}\n.oauth-row[data-v-79abbfea]{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;position:absolute;top:0;left:0;width:100%\n}\n.oauth-image[data-v-79abbfea]{width:100px;height:100px;border:1px solid #ddd;border-radius:100px;margin:0 40px;background-color:#fff\n}\n.oauth-image uni-image[data-v-79abbfea]{width:60px;height:60px;margin:20px\n}",""])}}]);