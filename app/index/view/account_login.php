<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta content="always" name="referrer">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>会员登录-{$_G['setting']['site_name']}</title>
	<base href="{$_G['site_url']}">
	<script src="static/js/jquery-3.3.1.min.js"></script>
	<script src="static/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="static/css/common.css">
	<link rel="stylesheet" type="text/css" href="static/css/signin.css">

	<style>
		html,body{ width: 100%; height: 100%; background: rgba(0, 255, 255, 0.3); }
		html{-webkit-tap-highlight-color: rgba(0, 0, 0, 0);}
		canvas{display:block;vertical-align:bottom;}
		#particles{ width: 100%; height: 100%; background-color: rgba(0, 0, 255, 0.6);position: absolute; left: 0; top: 0;right: 0;bottom: 0; z-index:-1;}
		.center-block {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
.center-block img{
	width: 70%;
	height: 43px;
	border: 1px solid #CCC;
	border-radius: 2px;
}
	</style>

</head>
<body class="text-center" style="background-image: url({$_G['setting']['_login']|default=''});background-repeat:no-repeat;background-size:100% 100%;-moz-background-size:100% 100%;">
	<div id="particles"></div>
	<form class="form-signin" autocomplete="off">
		<h2 class="mb-3 font-weight-normal text-light">会员登录</h2>
		<input type="text" id="account" class="form-control" name="account" placeholder="会员账号" required autofocusstyle="margin-bottom:10px;" style="margin-bottom:10px;">
		<input type="password" id="password" class="form-control" name="password" placeholder="登陆密码" required style="margin-bottom:10px;">

		<div class="input-group">
  		<input type="text" class="form-control" id="verifyCode" name="verifyCode" placeholder="验证码" aria-describedby="basic-addon2" style="margin-bottom:10px;">
  		<div class="input-group center-block" >
		{:captcha_img()}
	</div>
		
	</div>
	</div>
	



		<button class="btn btn-lg btn-danger btn-block btn-submit ajax-post mt-3" type="submit">登 录</button>
        <!--2019-06-29添加 start-->
        <a class="text-white btn btn-lg  btn-block mt-3" style="background-image: url({$_G['setting']['bg_reception_key']|default=''}); background-size: 100% 100%;" target="_blank" href="{$_G['setting']['reception_key_url']|default=''}">
            &emsp;
        </a>
        <!--2019-06-29添加 end-->
		<div class="d-flex justify-content-between mt-3">
			<div>
				{if $_G['setting']['allow_register']}
					<a class="text-light" href="{:url('index/account/register')}">注册账号</a>
				{/if}
			</div>
			<!-- <a class="text-light" href="{:url('index/account/get_password')}">找回密码</a> -->
		</div>
		<div class="mt-3 text-light">
        </div>
	</form>
	<script type="text/javascript" src="/static/js/common.js"></script>
	<script type="text/javascript" src="/static/js/particles.min.js"></script>
	<script type="text/javascript">
		$(function(){
			particlesJS('particles',
			  {
			    "particles": {
			      "number": {
			        "value": 80,
			        "density": {
			          "enable": true,
			          "value_area": 800
			        }
			      },
			      "color": {
			        "value": "#ffffff"
			      },
			      "shape": {
			        "type": "circle",
			        "stroke": {
			          "width": 0,
			          "color": "#000000"
			        },
			        "polygon": {
			          "nb_sides": 5
			        },
			        "image": {
			          "src": "img/github.svg",
			          "width": 100,
			          "height": 100
			        }
			      },
			      "opacity": {
			        "value": 0.5,
			        "random": false,
			        "anim": {
			          "enable": false,
			          "speed": 1,
			          "opacity_min": 0.1,
			          "sync": false
			        }
			      },
			      "size": {
			        "value": 5,
			        "random": true,
			        "anim": {
			          "enable": false,
			          "speed": 40,
			          "size_min": 0.1,
			          "sync": false
			        }
			      },
			      "line_linked": {
			        "enable": true,
			        "distance": 150,
			        "color": "#ffffff",
			        "opacity": 0.4,
			        "width": 1
			      },
			      "move": {
			        "enable": true,
			        "speed": 6,
			        "direction": "none",
			        "random": false,
			        "straight": false,
			        "out_mode": "out",
			        "attract": {
			          "enable": false,
			          "rotateX": 600,
			          "rotateY": 1200
			        }
			      }
			    },
			    "interactivity": {
			      "detect_on": "canvas",
			      "events": {
			        "onhover": {
			          "enable": false,
			          "mode": "repulse"
			        },
			        "onclick": {
			          "enable": true,
			          "mode": "push"
			        },
			        "resize": true
			      },
			      "modes": {
			        "grab": {
			          "distance": 400,
			          "line_linked": {
			            "opacity": 1
			          }
			        },
			        "bubble": {
			          "distance": 400,
			          "size": 40,
			          "duration": 2,
			          "opacity": 8,
			          "speed": 3
			        },
			        "repulse": {
			          "distance": 200
			        },
			        "push": {
			          "particles_nb": 4
			        },
			        "remove": {
			          "particles_nb": 2
			        }
			      }
			    },
			    "retina_detect": true,
			    "config_demo": {
			      "hide_card": false,
			      "background_color": "#b61924",
			      "background_image": "",
			      "background_position": "50% 50%",
			      "background_repeat": "no-repeat",
			      "background_size": "cover"
			    }
			  }

			);
		})
	</script>
	
</body>
</html>
