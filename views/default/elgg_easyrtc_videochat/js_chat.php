
var onlineUsers = {};

elgg.provide('elgg.nodeChat');

elgg.nodeChat.init = function() {

	require(['easyrtc', 'handlebars'], function() {

		// toggle chat box
		$('body').on('click', '.chat-box > .elgg-head, .chat-box > .elgg-head .elgg-menu-item-toggle a', function() {
			var $this = $(this),
				$cb = $this.closest('.chat-box');

			//if ($this.hasClass('elgg-head') && $cb.hasClass('elgg-state-active')) return false;
			$cb.toggleClass('elgg-state-active');
		});

		// close chat box
		$('body').on('click', '.chat-box > .elgg-head .elgg-menu-item-close a', function() {
			var $cb = $(this).closest('.chat-box');

			socket.emit('leave_room', $cb.data('channel'));
			$cb.remove();
		});

		// open room with an user
		$('#online-users').on('click', '.elgg-body ul li', function() {
			// check if room with this user is not already open
			var $this = $(this),
				$cb = $('.chat-room'),
				opened = 0;

			$.each($cb, function(i, e) {
				opened = $.grep($(e).data('data').add_users, function(u) {
					return u == $this.data('guid');
				});
				if (opened) return false;
			});

			var data = {
					channel: new Date().getTime() + (Math.random()+"").replace('0.','_'),
					type: 'chat',
					data: {
						chat_name: $this.data('name'),
						add_users: [elgg.get_logged_in_user_guid(), $this.data('guid')]
					}
				};

			elgg.nodeChat.openChatroom(data);
		});

		// Send message
		$('body').on('keydown', '.chat-box .chat-box-bottom textarea', function(evt) {
			var $this = $(this),
				msg = $.trim($this.val());

			if (!evt.shiftKey && evt.keyCode == 13) { // user can type shift+enter to write enter char
				if (msg.replace(/\s/g, '').length !== 0) { // Don't send just whitespace
					var $cb = $this.closest('.chat-box'),
						data = $.extend($cb.data(), {msg: msg});

					socket.emit('message', data, function() {
						elgg.nodeChat.addMessage($cb, $.extend(data, {sender: {
							guid: elgg.get_logged_in_user_guid(),
							name: elgg.get_logged_in_user_entity().name
						}}));
						$this.val('');
					});
				}
				return false;
			}
		});

	});

};
elgg.register_hook_handler('init', 'system', elgg.nodeChat.init);



elgg.nodeChat.connected = function(hook, type, params, value) {
	console.log(params, value);
	if (value && params) {
		$.each(params, function(i, user) {
			onlineUsers[user.guid] = user;
		});
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('socketIO', 'connected', elgg.nodeChat.connected);



elgg.nodeChat.add_online_user = function(hook, type, params, value) {
	console.log(params, value);
	if (value && params) {
		onlineUsers[params.guid] = params;
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('socketIO', 'add_online_user', elgg.nodeChat.add_online_user);



elgg.nodeChat.remove_online_user = function(hook, type, params, value) {
	console.log(params, 'remove_online_user'+value);
	if (value && params) {
		delete onlineUsers[params];
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('socketIO', 'remove_online_user', elgg.nodeChat.remove_online_user);



elgg.nodeChat.set_online_user = function() {
	// remove me
	delete onlineUsers[elgg.get_logged_in_user_guid()];

	var $ou = $('#online-users'),
		userCount = Object.keys(onlineUsers).length,
		onlineString = elgg.echo('videochat:online_user'+(userCount>1?'s':''), [userCount])

	$ou.removeClass('hidden').find('.elgg-head h4').html(onlineString);

	$ou.find('.elgg-body ul').html(Handlebars.compile($('#online-users-template').html())({users: onlineUsers}));
};



elgg.nodeChat.message = function(hook, type, params, value) {
	// if chat box doesn't exist
	if (!$('#chatroom-'+params.channel).length) {
		elgg.nodeChat.openChatroom(params);
	}

	var $cb = $('#chatroom-'+params.channel);

	elgg.nodeChat.addMessage($cb, params);

	return value;
};
elgg.register_hook_handler('socketIO', 'message:chat', elgg.nodeChat.message);



elgg.nodeChat.openChatroom = function(data) {
	$('#chat-container').prepend(Handlebars.compile($('#chatroom-template').html())(data));
	$('#chatroom-'+data.channel).data(data);
};



elgg.nodeChat.addMessage = function(chatElem, data) {
	var $cb = chatElem.find('.chat-body'),
		time = new Date();

	$.extend(data, {time: time.getHours() + ':' + time.getMinutes() + ':' + time.getSeconds()});
	if ($cb.children('li:last-child').data('guid') == data.sender.guid) {
		$cb.children('li:last-child').find('.elgg-body').append($('<p>', {html: data.msg, time: data.time}));
	} else {
		$cb.append(Handlebars.compile($('#chat-message-template').html())(data));
	}
	$cb.scrollTo(999999);
};






//'is typing' message
 /* var typing = false;
  var timeout = undefined;

  function timeoutFunction() {
    typing = false;
    socket.emit("typing", false);
  }

  $("#msg").keypress(function(e){
    if (e.which !== 13) {
      if (typing === false && myRoomID !== null && $("#msg").is(":focus")) {
        typing = true;
        socket.emit("typing", true);
      } else {
        clearTimeout(timeout);
        timeout = setTimeout(timeoutFunction, 5000);
      }
    }
  });

  socket.on("isTyping", function(data) {
    if (data.isTyping) {
      if ($("#"+data.person+"").length === 0) {
        $("#updates").append("<li id='"+ data.person +"'><span class='text-muted'><small><i class='fa fa-keyboard-o'></i> " + data.person + " is typing.</small></li>");
        timeout = setTimeout(timeoutFunction, 5000);
      }
    } else {
      $("#"+data.person+"").remove();
    }
  });*/