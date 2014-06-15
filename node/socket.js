
module.exports = function (io){

	/**
	 * Called when socket connect for the first time and socket.io ask for authorization token.
	 */
	io.sockets.on('connect', function(socket) {

		// Send Users to this socket only
		socket.emit('connected', getAllUsers());

		socket.on('chat_message', function(data, callback) {

			// add user to room ?
			if (data.room && data.users) {
				_.each(data.users, function(roomUser) {
					var user = _.find(Users, function(user) {
						return user.guid == roomUser.guid;
					});

					if (user && !_.contains(user.rooms, data.room)) {
						_.invoke(user.sockets, 'join', data.room); // join room for all sockets of this user
						user.Emit('join_room', Rooms[data.room]); // send rooms's data to all sockets of this user
						user.rooms.push(data.room); // add this room to user data
					}
				});
			}

			// add current user as sender
			var msg = _.extend(data.msg, {
				sender: Users[getElggSession(socket)].tiny(),
				date: new Date()
			});

			// add message type
			_.extend(data, {type: 'chat'});

			// check if room exist.
			if (!Rooms[data.room]) {
				Rooms[data.room] = _.extend(_.omit(data, ['msg', 'room_name']), {msgs: []});
			} else {
				_.extend(Rooms[data.room], _.omit(data, ['msg', 'room_name']));
			}

			// add message to room data msgs
			if (Rooms[data.room].msgs.length > 3) {
				Rooms[data.room].msgs.shift();
				Rooms[data.room].msgs.push(msg);
			} else {
				Rooms[data.room].msgs.push(msg);
			}
			delete msg;

			console.log(Rooms[data.room].msgs);
			socket.broadcast.to(data.room).send(data); // all client in room but not me

			callback();
		});
	});
}

