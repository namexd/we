(function(n){function e(e){for(var t,r,c=e[0],u=e[1],s=e[2],g=0,l=[];g<c.length;g++)r=c[g],i[r]&&l.push(i[r][0]),i[r]=0;for(t in u)Object.prototype.hasOwnProperty.call(u,t)&&(n[t]=u[t]);p&&p(e);while(l.length)l.shift()();return a.push.apply(a,s||[]),o()}function o(){for(var n,e=0;e<a.length;e++){for(var o=a[e],t=!0,r=1;r<o.length;r++){var u=o[r];0!==i[u]&&(t=!1)}t&&(a.splice(e--,1),n=c(c.s=o[0]))}return n}var t={},i={index:0},a=[];function r(n){return c.p+"static/js/"+({"pages-binding-binding":"pages-binding-binding","pages-login-login":"pages-login-login","pages-main-main":"pages-main-main","pages-pwd-pwd":"pages-pwd-pwd","pages-reg-reg":"pages-reg-reg","pages-user-user":"pages-user-user"}[n]||n)+"."+{"pages-binding-binding":"f52b55ae","pages-login-login":"250175f3","pages-main-main":"564a6c73","pages-pwd-pwd":"f6149891","pages-reg-reg":"f4ad2775","pages-user-user":"63af47bc"}[n]+".js"}function c(e){if(t[e])return t[e].exports;var o=t[e]={i:e,l:!1,exports:{}};return n[e].call(o.exports,o,o.exports,c),o.l=!0,o.exports}c.e=function(n){var e=[],o=i[n];if(0!==o)if(o)e.push(o[2]);else{var t=new Promise(function(e,t){o=i[n]=[e,t]});e.push(o[2]=t);var a,u=document.getElementsByTagName("head")[0],s=document.createElement("script");s.charset="utf-8",s.timeout=120,c.nc&&s.setAttribute("nonce",c.nc),s.src=r(n),a=function(e){s.onerror=s.onload=null,clearTimeout(g);var o=i[n];if(0!==o){if(o){var t=e&&("load"===e.type?"missing":e.type),a=e&&e.target&&e.target.src,r=new Error("Loading chunk "+n+" failed.\n("+t+": "+a+")");r.type=t,r.request=a,o[1](r)}i[n]=void 0}};var g=setTimeout(function(){a({type:"timeout",target:s})},12e4);s.onerror=s.onload=a,u.appendChild(s)}return Promise.all(e)},c.m=n,c.c=t,c.d=function(n,e,o){c.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:o})},c.r=function(n){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},c.t=function(n,e){if(1&e&&(n=c(n)),8&e)return n;if(4&e&&"object"===typeof n&&n&&n.__esModule)return n;var o=Object.create(null);if(c.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var t in n)c.d(o,t,function(e){return n[e]}.bind(null,t));return o},c.n=function(n){var e=n&&n.__esModule?function(){return n["default"]}:function(){return n};return c.d(e,"a",e),e},c.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},c.p="/",c.oe=function(n){throw console.error(n),n};var u=window["webpackJsonp"]=window["webpackJsonp"]||[],s=u.push.bind(u);u.push=e,u=u.slice();for(var g=0;g<u.length;g++)e(u[g]);var p=s;a.push([0,"chunk-vendors"]),o()})({0:function(n,e,o){n.exports=o("64de")},"18ad":function(n,e,o){"use strict";var t=o("d02e"),i=o.n(t);i.a},"64de":function(n,e,o){"use strict";o.r(e);o("744f"),o("6c7b"),o("7514"),o("20d6"),o("1c4c"),o("6762"),o("cadf"),o("e804"),o("55dd"),o("d04f"),o("0298"),o("c8ce"),o("87b3"),o("217b"),o("7f7f"),o("f400"),o("7f25"),o("536b"),o("d9ab"),o("f9ab"),o("32d7"),o("25c9"),o("9f3c"),o("042e"),o("c7c6"),o("f4ff"),o("049f"),o("7872"),o("a69f"),o("0b21"),o("6c1a"),o("c7c62"),o("84b4"),o("c5f6"),o("2e37"),o("fca0"),o("7cdf"),o("ee1d"),o("b1b1"),o("87f3"),o("9278"),o("5df2"),o("04ff"),o("f751"),o("4504"),o("fee7"),o("ffc1"),o("0d6d"),o("9986"),o("8e6e"),o("25db"),o("e4f7"),o("b9a1"),o("64d5"),o("9aea"),o("db97"),o("66c8"),o("57f0"),o("165b"),o("456d"),o("cf6a"),o("fd24"),o("8615"),o("551c"),o("097d"),o("df1b"),o("2397"),o("88ca"),o("ba16"),o("d185"),o("ebde"),o("2d34"),o("f6b3"),o("2251"),o("c698"),o("a19f"),o("9253"),o("9275"),o("3b2b"),o("3846"),o("4917"),o("a481"),o("28a5"),o("386d"),o("6b54"),o("4f7f"),o("8a81"),o("ac4d"),o("8449"),o("9c86"),o("fa83"),o("48c0"),o("a032"),o("aef6"),o("d263"),o("6c37"),o("9ec8"),o("5695"),o("2fdb"),o("d0b0"),o("5df3"),o("b54a"),o("f576"),o("ed50"),o("788d"),o("14b9"),o("f386"),o("f559"),o("1448"),o("673e"),o("242a"),o("c66f"),o("262f"),o("b05c"),o("34ef"),o("6aa2"),o("15ac"),o("af56"),o("b6e4"),o("9c29"),o("63d9"),o("4dda"),o("10ad"),o("c02b"),o("4795"),o("130f"),o("ac6a"),o("96cf"),o("b9ad"),o("6e8c"),o("1c31");var t=o("e143"),i=function(){var n=this,e=n.$createElement,o=n._self._c||e;return o("App",{attrs:{keepAliveInclude:n.keepAliveInclude}})},a=[],r={onLaunch:function(){console.log("App Launch")},onShow:function(){console.log("App Show")},onHide:function(){console.log("App Hide")}},c=r,u=(o("18ad"),o("2877")),s=Object(u["a"])(c,i,a,!1,null,null,null);s.options.__file="App.vue";var g=s.exports,p=o("2f62");t["default"].use(p["a"]);var l=new p["a"].Store({state:{forcedLogin:!1,hasLogin:!1,userName:""},mutations:{login:function(n,e){n.userName=e||"新用户",n.hasLogin=!0},logout:function(n){n.userName="",n.hasLogin=!1}}}),f=l;function d(n){for(var e=1;e<arguments.length;e++){var o=null!=arguments[e]?arguments[e]:{},t=Object.keys(o);"function"===typeof Object.getOwnPropertySymbols&&(t=t.concat(Object.getOwnPropertySymbols(o).filter(function(n){return Object.getOwnPropertyDescriptor(o,n).enumerable}))),t.forEach(function(e){m(n,e,o[e])})}return n}function m(n,e,o){return e in n?Object.defineProperty(n,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):n[e]=o,n}t["default"].config.productionTip=!1,t["default"].prototype.$store=f,g.mpType="app";var b=new t["default"](d({store:f},g));b.$mount()},"6e8c":function(n,e,o){(function(n){n.__uniConfig.router={mode:"hash",base:"/"},n.__uniConfig["async"]={loading:"AsyncLoading",error:"AsyncError",delay:200,timeout:3e3},n.__uniConfig.debug=!1,n.__uniConfig.networkTimeout={request:6e3,connectSocket:6e3,uploadFile:6e3,downloadFile:6e3}}).call(this,o("c8ba"))},"9c6b":function(n,e,o){e=n.exports=o("2350")(!1),e.push([n.i,'\nuni-page-body{min-height:100%\n}\n.content,uni-page-body{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex\n}\n.content{-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;background-color:#efeff4;padding:20px\n}\n.input-group{background-color:#fff;margin-top:40px;position:relative\n}\n.input-group:before{top:0\n}\n.input-group:after,.input-group:before{position:absolute;right:0;left:0;height:1px;content:"";-webkit-transform:scaleY(.5);-ms-transform:scaleY(.5);transform:scaleY(.5);background-color:#c8c7cc\n}\n.input-group:after{bottom:0\n}\n.input-row{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;position:relative\n}\n.input-row .title{width:20%;padding:15px 0;padding-left:30px\n}\n.input-row .title,.input-row uni-input{height:50px;min-height:50px;line-height:50px\n}\n.input-row uni-input{width:80%;padding:15px 0;padding-right:30px\n}\n.input-row.border:after{position:absolute;right:0;bottom:0;left:15px;height:1px;content:"";-webkit-transform:scaleY(.5);-ms-transform:scaleY(.5);transform:scaleY(.5);background-color:#c8c7cc\n}\n.btn-row{margin-top:50px;padding:20px\n}\nuni-button.primary{background-color:#0faeff\n}',""])},b9ad:function(n,e,o){"use strict";(function(n){var e=o("e143");n.__uniConfig={tabBar:{color:"#7a7e83",selectedColor:"#0faeff",backgroundColor:"#ffffff",list:[{pagePath:"pages/main/main",text:"首页",iconPath:"static/img/home.png",selectedIconPath:"static/img/homeHL.png",redDot:!1,badge:""},{pagePath:"pages/user/user",text:"我的",iconPath:"static/img/user.png",selectedIconPath:"static/img/userHL.png",redDot:!1,badge:""}],borderStyle:"black"},globalStyle:{navigationBarTextStyle:"white",navigationBarBackgroundColor:"#0faeff",backgroundColor:"#fbf9fe"}},e["default"].component("pages-main-main",function(n){var e={component:o.e("pages-main-main").then(function(){return n(o("2cb2"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),e["default"].component("pages-login-login",function(n){var e={component:o.e("pages-login-login").then(function(){return n(o("9e30"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),e["default"].component("pages-reg-reg",function(n){var e={component:o.e("pages-reg-reg").then(function(){return n(o("1275"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),e["default"].component("pages-pwd-pwd",function(n){var e={component:o.e("pages-pwd-pwd").then(function(){return n(o("5f30"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),e["default"].component("pages-user-user",function(n){var e={component:o.e("pages-user-user").then(function(){return n(o("dfd8"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),e["default"].component("pages-binding-binding",function(n){var e={component:o.e("pages-binding-binding").then(function(){return n(o("3a3d"))}.bind(null,o)).catch(o.oe),delay:__uniConfig["async"].delay,timeout:__uniConfig["async"].timeout};return __uniConfig["async"]["loading"]&&(e.loading={name:"SystemAsyncLoading",render:function(n){return n(__uniConfig["async"]["loading"])}}),__uniConfig["async"]["error"]&&(e.error={name:"SystemAsyncError",render:function(n){return n(__uniConfig["async"]["error"])}}),e}),n.__uniRoutes=[{path:"/",alias:"/pages/main/main",component:{render:function(n){return n("Page",{props:Object.assign({isQuit:!0,isEntry:!0,isTabBar:!0,tabBarIndex:0},__uniConfig.globalStyle,{navigationBarTitleText:"登录模板"})},[n("pages-main-main",{slot:"page"})])}},meta:{id:1,name:"pages-main-main",pagePath:"pages/main/main",isQuit:!0,isEntry:!0,isTabBar:!0,tabBarIndex:0,windowTop:44}},{path:"/pages/login/login",component:{render:function(n){return n("Page",{props:Object.assign({},__uniConfig.globalStyle,{navigationBarTitleText:"登录"})},[n("pages-login-login",{slot:"page"})])}},meta:{name:"pages-login-login",pagePath:"pages/login/login",windowTop:44}},{path:"/pages/reg/reg",component:{render:function(n){return n("Page",{props:Object.assign({},__uniConfig.globalStyle,{navigationBarTitleText:"注册"})},[n("pages-reg-reg",{slot:"page"})])}},meta:{name:"pages-reg-reg",pagePath:"pages/reg/reg",windowTop:44}},{path:"/pages/pwd/pwd",component:{render:function(n){return n("Page",{props:Object.assign({},__uniConfig.globalStyle,{navigationBarTitleText:"找回密码"})},[n("pages-pwd-pwd",{slot:"page"})])}},meta:{name:"pages-pwd-pwd",pagePath:"pages/pwd/pwd",windowTop:44}},{path:"/pages/user/user",component:{render:function(n){return n("Page",{props:Object.assign({isQuit:!0,isTabBar:!0,tabBarIndex:1},__uniConfig.globalStyle,{navigationBarTitleText:"我的"})},[n("pages-user-user",{slot:"page"})])}},meta:{id:2,name:"pages-user-user",pagePath:"pages/user/user",isQuit:!0,isTabBar:!0,tabBarIndex:1,windowTop:44}},{path:"/pages/binding/binding",component:{render:function(n){return n("Page",{props:Object.assign({},__uniConfig.globalStyle,{navigationBarTitleText:"绑定账号"})},[n("pages-binding-binding",{slot:"page"})])}},meta:{name:"pages-binding-binding",pagePath:"pages/binding/binding",windowTop:44}},{path:"/preview-image",component:{render:function(n){return n("Page",{props:{navigationStyle:"custom"}},[n("system-preview-image",{slot:"page"})])}},meta:{name:"preview-image",pagePath:"/preview-image"}},{path:"/choose-location",component:{render:function(n){return n("Page",{props:{navigationStyle:"custom"}},[n("system-choose-location",{slot:"page"})])}},meta:{name:"choose-location",pagePath:"/choose-location"}},{path:"/open-location",component:{render:function(n){return n("Page",{props:{navigationStyle:"custom"}},[n("system-open-location",{slot:"page"})])}},meta:{name:"open-location",pagePath:"/open-location"}}]}).call(this,o("c8ba"))},d02e:function(n,e,o){var t=o("9c6b");"string"===typeof t&&(t=[[n.i,t,""]]),t.locals&&(n.exports=t.locals);var i=o("4f06").default;i("5d985011",t,!0,{sourceMap:!1,shadowMode:!1})}});