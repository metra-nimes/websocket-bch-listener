import 'bootstrap';
import jQuery from 'jquery';
import 'bootstrap/dist/css/bootstrap.min.css';
import './custom.css';

jQuery(function($){
	let socket = new WebSocket("ws://localhost:5000"),
		connectionLabel = $('[data-label="ws-connected"]'),
		transactionTable = $('.list-group.transaction-table'),
		blockTable = $('.list-group.block-table');

	socket.onmessage = function(e){
		let data = JSON.parse(e.data),
			result = JSON.parse(data[2]);

		if (result) {
			blockTable.html('');
			transactionTable.html('');
			result.forEach(function(item){
				blockTable.append('<li class="list-group-item d-flex justify-content-center align-items-center text-dark">'+item.hash+'</li>').hide().show('slow');
				item.transactions.forEach(function(transaction){
					transactionTable.append('<li class="list-group-item d-flex justify-content-center align-items-center text-dark">'+transaction+'</li>').hide().show('slow');
				})
			})
		}
	};
	socket.onopen = () => {
		connectionLabel.html('<strong>Websocket:</strong> connected');
	};
	socket.onerror = () => {
		connectionLabel.html('<strong>Websocket:</strong> Error');
	};
});

