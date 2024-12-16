/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

// Function to create types graphics in datapool.php
createGraphic = function(tableId, dataValues, dataKeys){

	//	get table size
	let table = document.getElementById('table-' + tableId);
	let tableBody = table.querySelector('tbody').clientHeight;
	let tableHead = table.querySelector('thead').clientHeight;

	let canvas = document.getElementById('canvas-' + tableId);
	let data = [{
		x: dataValues,
		y: dataKeys,
		type: "bar",
		orientation: 'h'
	}];
	let layout = {
		autosize: true,
		width: 400,
		height: tableBody,
		margin: {'t': tableHead, 'r': 0, 'b': 0, 'l': 10},
		yaxis: {
			showticklabels: false,
			fixedrange: true
		},
		xaxis: {
			fixedrange: true
		}
	};

	config = {responsive: true,displayModeBar: false};
	plot = Plotly.newPlot(canvas, data, layout, config);

	// console.log(tableHeight);
};

$(function(){

	let charts = document.querySelectorAll('.analytics-barchart');

	charts.forEach(function(div){
		let tableId = div.dataset.datasource;
		let table = document.getElementById('table-' + tableId);

		let firstCol = table.querySelectorAll('tbody tr.analytics-row td:first-child');
		let lastCol = table.querySelectorAll('tbody tr.analytics-row td:last-child');

		let dataKeys = Array.from(firstCol).reverse().map(
			function(td) {return td.textContent; }
		);

		let dataValues = Array.from(lastCol).reverse().map(
			function(td) {return parseInt(td.textContent); }
		);
			createGraphic(tableId, dataValues, dataKeys);
		}
		);
});


