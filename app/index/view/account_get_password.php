<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta content="always" name="referrer">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>密码找回-{$_G['setting']['site_name']}</title>
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
	</style>
</head>
<body class="text-center">
	<div id="particles"></div>
	<form class="form-signin" autocomplete="off">
		<h2 class="mb-3 font-weight-normal text-light">自助找回密码</h2>
		<input type="text" id="email" class="form-control" name="email" placeholder="绑定的邮箱地址" required autofocus style="border-bottom-left-radius: 0;border-bottom-right-radius: 0;border-bottom: 0;">
		<div class="input-group mb-3">
			<input type="text" class="form-control" name="verify_code" placeholder="验证码" style="border-top-left-radius: 0;">
			<div class="input-group-append get-verify-code" style="cursor: pointer;user-select: none;">
				<span class="input-group-text" style="border-top-right-radius: 0;">获取验证码</span>
			</div>
		</div>
		<button class="btn btn-lg btn-danger btn-block btn-submit ajax-post mt-3" type="submit">验证并重置密码</button>
		<div class="d-flex justify-content-between mt-3">
			<div>
				{if $_G['setting']['allow_register']}
					<a class="text-light" href="{:url('index/account/register')}">注册账号</a>
				{/if}
			</div>
			<a class="text-light" href="{:url('index/account/login')}">立即登录</a>
		</div>
		<div class="mt-3 text-light">&copy; 2019-2099</div>
	</form>
	<script type="text/javascript" src="static/js/common.js"></script>
	<script type="text/javascript" src="static/js/particles.min.js"></script>
	<script>
		$(function(){
			$(document)
				.on('click','.get-verify-code',function(){
					if($(this).hasClass('disabled')){
						return false;
					}
					var email = $('input[name="email"]').val();
					if(!email){
						return dialog.msg('请输入绑定邮箱');
					}
					$('.get-verify-code').addClass('disabled').find('span').html('发送中');
					$.post(
						'{:url('index/account/get_verify_code')}',
						{email:email},
						function(s){
							$('.get-verify-code').removeClass('disabled').find('span').html(s.code == 1 ? '发送成功' : '发送失败');
							dialog.msg(s);
						},
						'json'
					)
				})
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
