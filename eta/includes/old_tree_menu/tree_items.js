/* 
	the format of the tree definition file is simple,
	you can find specification in the Tigra Menu documentation at:
	
	http://www.softcomplex.com/products/tigra_tree_menu/docs/index.html#hierarchy
*/

var TREE_ITEMS = [
	['Trades', 0, 
		['Summary'],
		['Trade Details', 0,
			['client'],
			['closed'],
			['document', 0, 
				['Description'],
				['Example']
			],
		],
		['Employee Trades', 0, 
			['alert'],
			['blur'],
			['clearInterval'],
			['clearTimeout'],
			['close'],
			['confirm'],
			['execScript'],
			['focus '],
			['navigate'],
			['open'],
			['prompt'],
			['scroll'],
			['setInterval'],
			['setTimeout'],
			['showHelp'],
			['showModalDialog']
		],
		['Flagged Trades', 0,
			['frames']
		],
	]
];