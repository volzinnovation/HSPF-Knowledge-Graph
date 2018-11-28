
var cbx; 

$(function() {

    var layoutPadding = 50;
    var aniDur = 500;
    var easing = 'linear';

    var cy;

    // also get style via ajax
    var styleP = $.ajax({
        url: './style.cycss', 
        type: 'GET',
        dataType: 'text'
    });
		
    var infoTemplate = Handlebars.compile([
        '<p class="ac-name">{{name}}</p>',
        '<p class="ac-node-type"><i class="fa fa-info-circle"></i> {{NodeTypeFormatted}}</p>',
        '<p class="ac-more"><i class="fa fa-external-link"></i> <a target="_blank" href = "https://www.hs-pforzheim.de/suche/?L=0&q={{name}}"> Mehr über {{name}}... </a></p > '
    ].join(''));

    Promise.all([styleP]).then(function (){});
   
    var allNodes = null;
    var allEles = null;
    var lastHighlighted = null;
    var lastUnhighlighted = null;
    
    
    function getFadePromise(ele, opacity) {
        return ele.animation({
            style: { 'opacity': opacity },
            duration: aniDur
        }).play().promise();
    };

    function isDirty() {
        return lastHighlighted != null;
    }

    function clear(opts) {
        if (!isDirty()) { return Promise.resolve(); }

        opts = $.extend({

        }, opts);

        cy.stop();
        allNodes.stop();

        var nhood = lastHighlighted;
        var others = lastUnhighlighted;

        lastHighlighted = lastUnhighlighted = null;

        var hideOthers = function() {
            return Promise.delay(125).then(function() {
                others.addClass('hidden');

                return Promise.delay(125);
            });
        };

        var showOthers = function() {
            cy.batch(function() {
                allEles.removeClass('hidden').removeClass('faded');
            });

            return Promise.delay(aniDur);
        };

        var restorePositions = function() {
            cy.batch(function() {
                others.nodes().forEach(function(n) {
                    var p = n.data('orgPos');

                    n.position({ x: p.x, y: p.y });
                });
            });

            return restoreElesPositions(nhood.nodes());
        };

        var resetHighlight = function() {
            nhood.removeClass('highlighted');
        };

        return Promise.resolve()
            .then(resetHighlight)
            .then(hideOthers)
            .then(restorePositions)
            .then(showOthers);
    }

    function initCy(then) 
	{
		console.log('initCy entry')
        var loading = document.getElementById('loading');
	    var styleJson = styleP.responseText; 
		var nodes = then[0];
        
        cy = window.cy = cytoscape({
            container: document.getElementById('cy'),
            layout: { name: 'preset', padding: layoutPadding },
            style: styleJson,
            elements: null,
            motionBlur: true,
            selectionType: 'single',
            boxSelectionEnabled: false,
            autoungrabify: true
        });
        
		cy.add(nodes);
		
        allNodes = cy.nodes();
        allEles = cy.elements();

		cy.on('free', 'node', function(e) {
            var n = e.cyTarget;
            var p = n.position();

            n.data('orgPos', {
                x: p.x,
                y: p.y
            });
        });
 
        cy.on('tap', function() {
            $('#search').blur();
        }); 
		
		cy.on('select unselect', 'node', _.debounce(function(e) {
		
		var nodeData = e.cyTarget._private.data;
		
		var requestedId = nodeData.id;
		
		var p = {'x':500, 'y':500};
	 
	 
		var rerunLayout = function(then) {
	        
			var nodes = cy.nodes();
	       
            var r = nodes.makeLayout({
                name: 'concentric',
                fit: true,
				padding: 30,
				clockwise: true,
                animate: true,
                animationDuration: aniDur,
                animationEasing: easing,
                boundingBox: {
                    x1: p.x - 1,
                    x2: p.x + 1,
                    y1: p.y - 1,
                    y2: p.y + 1
                },
                avoidOverlap: false,
                concentric: function(ele) 
				{
                    if (ele.data('id') == requestedId)					
					{
                        return 2;
                    } 
					else 
					{
                        return 1;
                    }
                },
                levelWidth: function() { return 1; },
                padding: layoutPadding
            });

            var promise = cy.promiseOn('layoutstop');
        	
            r.run();

			
            return promise;
        };
     
		var rerunFit = function(then) {
            return cy.animation({
                fit: {
                    eles: nodes,
                    padding: layoutPadding
                },
                easing: easing,
                duration: aniDur
            }).play().promise();
        };
		
		var querynav; 
		if (nodeData.classes == 'Course')
			querynav = './rest.php/matchcourse?courseId='+requestedId;
		else if (nodeData.classes == 'Prof') 	
			querynav = './rest.php/matchprof?profId='+requestedId;
		
	 	var queryN = $.ajax({
						url: querynav,
						type: 'GET',
						dataType: 'json'
					});
		
		$('#sectionhide').hide();
		
		Promise.all([queryN]).then(initCy).then(rerunLayout).then(rerunFit);	
		
		},
		));
		
		
    }

    var lastSearch = '';
	
	
    $('#search').typeahead({
        minLength: 2,
        highlight: true,
    }, {
        name: 'search-dataset',
        source: function(query, cb) {
                 
            cbx = cb;
			
            var res = $.ajax({
						url: './rest.php/search?q='+query,
						type: 'GET',
						dataType: 'json'
					}).done(function( data ) {
					cbx(data);});
        },
        templates: {
            suggestion: infoTemplate
        }
    }).on('typeahead:selected', function(e, entry, dataset) {
		
		var requestedId;
		if (entry.NodeTypeFormatted == 'Kurs')
			requestedId = entry.IDcourse
		else if (entry.NodeTypeFormatted == 'Angestellter') 	
			requestedId = entry.IDprof
		
		var p = {'x':500, 'y':500};
	 
	 
		var doLayout = function(then) {
	        
			var nodes = cy.nodes();
	       
            var l = nodes.makeLayout({
                name: 'concentric',
                fit: true,
				padding: 30,
				clockwise: true,
                animate: true,
                animationDuration: aniDur,
                animationEasing: easing,
                boundingBox: {
                    x1: p.x - 1,
                    x2: p.x + 1,
                    y1: p.y - 1,
                    y2: p.y + 1
                },
                avoidOverlap: false,
                concentric: function(ele) 
				{
                    if (ele.data('id') == requestedId)					
					{
                        return 2;
                    } 
					else 
					{
                        return 1;
                    }
                },
                levelWidth: function() { return 1; },
                padding: layoutPadding
            });

            var promise = cy.promiseOn('layoutstop');
        	
            l.run();

			
            return promise;
        };
     
	var doFit = function(then) {
            return cy.animation({
                fit: {
                    eles: nodes,
                    padding: layoutPadding
                },
                easing: easing,
                duration: aniDur
            }).play().promise();
        };
		
		var queryUrl; 
		if (entry.NodeTypeFormatted == 'Kurs')
			queryUrl = './rest.php/matchcourse?courseId='+entry.IDcourse
		else if (entry.NodeTypeFormatted == 'Angestellter') 	
			queryUrl = './rest.php/matchprof?profId='+entry.IDprof
		
	 	var queryR = $.ajax({
						url: queryUrl,
						type: 'GET',
						dataType: 'json'
					});
		
		$('#sectionhide').hide();
		
		Promise.all([queryR]).then(initCy).then(doLayout).then(doFit);		
		
	
    })

    $('#reset').on('click', function() {
        if (isDirty()) {
            clear();
        } else {
            allNodes.unselect();

            hideNodeInfo();

            cy.stop();

            cy.animation({
                fit: {
                    eles: cy.elements(),
                    padding: layoutPadding
                },
                duration: aniDur,
                easing: easing
            }).play();
        }
    });

    $('#filter').qtip({
        position: {
            my: 'top center',
            at: 'bottom center',
            adjust: {
                method: 'shift'
            },
            viewport: true
        },

        show: {
            event: 'click'
        },

        hide: {
            event: 'unfocus'
        },

        style: {
            classes: 'qtip-bootstrap qtip-filters',
            tip: {
                width: 16,
                height: 8
            }
        },

        content: $('#filters')
    });

    $('#about').qtip({
        position: {
            my: 'bottom center',
            at: 'top center',
            adjust: {
                method: 'shift'
            },
            viewport: true
        },

        show: {
            event: 'click'
        },

        hide: {
            event: 'unfocus'
        },

        style: {
            classes: 'qtip-bootstrap qtip-about',
            tip: {
                width: 16,
                height: 8
            }
        },

        content: $('#about-content')
    });
});