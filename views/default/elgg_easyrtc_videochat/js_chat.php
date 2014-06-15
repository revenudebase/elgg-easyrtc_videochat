
var onlineUsers = {};

elgg.provide('elgg.nodeChat');

elgg.nodeChat.init = function() {

	require(['socket.io-client'], function() {

		// toggle chat box
		$('body').on('click', '.chat-box > .elgg-head, .chat-box > .elgg-head .elgg-menu-item-toggle a', function() {
			var $this = $(this),
				$cb = $this.closest('.chat-box');

			//if ($this.hasClass('elgg-head') && $cb.hasClass('elgg-state-active')) return false;
			$cb.toggleClass('elgg-state-active');
		});

		// close chat room
		$('body').on('click', '.chat-room > .elgg-head .elgg-menu-item-close a', function() {
			var $cb = $(this).closest('.chat-room');

			socket.emit('leave_room', $cb.data('room')); // @todo need callback that sure user leave room on server ?
			$cb.remove();
		});

		// open room with an user
		$('#online-users').on('click', '.elgg-body ul li', function() {
			var $this = $(this),
				myGUID = elgg.get_logged_in_user_guid(),
				calledGUID = $this.data('guid'),
				$cb = $('.chat-room'),
				opened = 0;

			$.each($cb, function(i, e) { // check if room with this user is not already open
				var users = $(e).data('users');
				if (users.length == 2) {
					var openedRoomUsers = [calledGUID+''+myGUID, myGUID+''+calledGUID],
						thisRoom = users[0].guid+''+users[1].guid;

					if ($.inArray(thisRoom, openedRoomUsers) > -1) opened = $(e);
				}
			});

			if (opened) {
				opened.find('textarea').focus().parent().effect('highlight', {}, 1000);
			} else {
				var roomID = elgg.getToken(),
					data = {
						room: roomID,
						users: [elgg.nodejs.me(), onlineUsers[calledGUID]]
					};

				elgg.nodeChat.openChatroom(data);
				$('#chatroom-'+roomID).find('textarea').focus();
			}
		});

		// search user
		var Fuse, fuseUsers = [];
		$('#search-online-users')
		.on('keyup', 'input', function() {
			var val = $(this).val(),
				$users = $('#online-users .elgg-item-user');

			if (val.length === 0) {
				$users.show();
			} else {
				require(['fuse'], function(Fuse) {
					var $ouUl = $('#online-users .elgg-body ul'),
						fuseUsers = [],
						fuse = new Fuse(fuseUsers, {
							keys: ['name']
						});

					$ouUl.css('height', $ouUl.height());

					$.each(onlineUsers, function(i, user) {
						fuseUsers.push(user);
					});

					$.each(fuse.search(val), function(i, elem) {
						$users = $users.not($('#online-users #elgg-user-'+elem.guid));
					});
					$users.fadeOut('fast');
				});
			}
		})
		.on('blur', 'input', function() {
			if (!$(this).val()) $('#online-users .elgg-body ul').css('height', 'auto');
		});

		// Send message
		$('body').on('keyup', '.chat-room .chat-box-bottom textarea', function(evt) {
			var $this = $(this),
				msg = $.trim($this.val());

			if (!evt.shiftKey && evt.keyCode == 13) { // user can type shift+enter to write enter char
				if (msg.replace(/\s/g, '').length !== 0) { // Don't send just whitespace
					var $cb = $this.closest('.chat-box'),
						date = new Date(),
						data = $.extend($cb.data(), {
							msg: {
								text: msg,
								date: date.toString()
							}
						});

					socket.emit('chat_message', data, function() {
						$.extend(data.msg, {sender: elgg.nodejs.me()});
						elgg.nodeChat.addMessage($cb, data);
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
	if (value && params) {
		$.each(params, function(i, user) {
			onlineUsers[user.guid] = user;
		});
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('nodejs', 'connected', elgg.nodeChat.connected);



elgg.nodeChat.add_online_user = function(hook, type, params, value) {
	if (value && params) {
		onlineUsers[params.guid] = params;
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('nodejs', 'add_online_user', elgg.nodeChat.add_online_user);



elgg.nodeChat.remove_online_user = function(hook, type, params, value) {
	if (value && params) {
		delete onlineUsers[params];
		elgg.nodeChat.set_online_user();
	}
	return value;
};
elgg.register_hook_handler('nodejs', 'remove_online_user', elgg.nodeChat.remove_online_user);



elgg.nodeChat.set_online_user = function() {
	// remove me
	delete onlineUsers[elgg.get_logged_in_user_guid()];

	var $ou = $('#online-users'),
		userCount = Object.keys(onlineUsers).length,
		onlineString = elgg.echo('videochat:online_user'+(userCount>1?'s':''), [userCount]);

	$ou.removeClass('hidden').find('.elgg-head h4').html(onlineString);

	$ou.find('.elgg-body ul').html(elgg.handlebars('online-users-template')({users: onlineUsers}));
};



elgg.nodeChat.message = function(hook, type, params, value) {
	// if chat box doesn't exist
	if (!$('#chatroom-'+params.room).length) {
		elgg.nodeChat.openChatroom(params);
	}

	var $cb = $('#chatroom-'+params.room);

	elgg.nodeChat.addMessage($cb, params);
	elgg.notify();

	return value;
};
elgg.register_hook_handler('nodejs', 'message:chat', elgg.nodeChat.message);



elgg.nodeChat.join_room = function(hook, type, params, value) {
	if (!$('#chatroom-'+params.room).length) {
		elgg.nodeChat.openChatroom(params);

		var $chatElem = $('#chatroom-'+params.room);

		$.each(params.msgs, function(i, msg) {
			$.extend(params, {msg: msg});
			elgg.nodeChat.addMessage($chatElem, params);
		});
	}

	return value;
};
elgg.register_hook_handler('nodejs', 'join_room', elgg.nodeChat.join_room);



elgg.nodeChat.openChatroom = function(data) {
	var room_name = '';

	$.each(data.users, function(i, e) {
		if (e.guid != elgg.get_logged_in_user_guid()) room_name += (room_name.length == 0) ? e.name : ', '+e.name;
	});
	$.extend(data, {room_name: room_name});
	$('#chat-container').prepend(elgg.handlebars('chatroom-template')(data));
	$('#chatroom-'+data.room).data(data);
};



elgg.nodeChat.addMessage = function(chatElem, data) {
	var $cb = chatElem.find('.chat-body'),
		$cbLast = $cb.children('li:last-child'),
		date = new Date(data.msg.date),
		addZero = function(num) {
			return (num < 10) ? '0'+num : num;
		};

	data.msg.date = addZero(date.getHours()) + ':' + addZero(date.getMinutes()) + ':' + addZero(date.getSeconds());
	if ($cbLast.length && $cbLast.data('msg').sender.guid == data.msg.sender.guid) {
		$cbLast.find('.elgg-body').append($('<p>', {html: data.msg.text, date: data.msg.date}));
	} else {
		var message = elgg.handlebars('chat-message-template');
		$cb.append($(message(data)).data(data));
	}
	$cb.scrollTop($cb[0].scrollHeight);
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