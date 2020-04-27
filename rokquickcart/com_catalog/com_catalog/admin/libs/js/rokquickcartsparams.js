/**
 * @version $Id: dynamic-params.js 6850 2013-01-28 18:13:53Z btowles $
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

((function(){

	var RokQuickCartParams = new Class({
		Implements: [Options],
		options: {},
		initialize: function(options){
			this.setOptions(options);
			this.container = document.id(this.options.container);

			var bounds = {
				addblock: this.addBlock.bind(this),
				remblock: this.remBlock.bind(this),
				addrow: this.addRow.bind(this),
				remrow: this.remRow.bind(this),
				refresh: function(e, element){
					var block = element.getParent('.rokquickcart-field-block');
					this.refresh(block);
				}.bind(this),
				cleanBlocks: this.cleanBlocks.bind(this)
			};

			$(document.body).addEvents({
				'click:relay([data-rqc-addblock])': bounds.addblock,
				'click:relay([data-rqc-remblock])': bounds.remblock,
				'click:relay([data-rqc-addrow])': bounds.addrow,
				'click:relay([data-rqc-remrow])': bounds.remrow,
				'blur:relay(.rokquickcart-key input[type=text])': bounds.refresh
			});

			document.getElement('form[name=adminForm]').addEvent('submit', bounds.cleanBlocks);
		},

		addBlock: function(e, element){
			if (e) e.preventDefault();
			var dummy = new Element('div').set('html', template),
				block = dummy.getFirst(),
				blocks = this.container.getElements('[data-rqc-remblock]');

			if (!blocks) this.container.empty();

			this.container.adopt(block);
			block.getElement('.rokquickcart-key input[type=text]').focus();
		},

		remBlock: function(e, element){
			if (e) e.preventDefault();

			element.getParent('.rokquickcart-field-block').dispose();

			var blocks = this.container.getElements('[data-rqc-remblock]');

			if (!blocks.length){
				new Element('input[type=hidden][name="jform[params][custom_fields]"]').inject(this.container);
			}
		},

		addRow: function(e, element){
			if (e) e.preventDefault();
			var dummy  = new Element('div').set('html', row),
				prev   = element.getParent('.rokquickcart-value-item');

			dummy.getFirst().inject(prev, 'after');

			if (element.getParent('.btn-group').hasClass('alone')) element.getParent('.btn-group').removeClass('alone');

			this.refresh(element.getParent('.rokquickcart-field-block'));
		},

		remRow: function(e, element){
			if (e) e.preventDefault();
			var parent = element.getParent('.rokquickcart-value');

			element.getParent('.rokquickcart-value-item').dispose();

			var items = parent.getElements('.rokquickcart-value-item');

			if (items.length == 1) parent.getElement('.btn-group').addClass('alone');

			this.refresh(parent.getParent('.rokquickcart-field-block'));
		},

		refresh: function(block){
			var label = block.getElement('.rokquickcart-key input[type=text]'),
				opts  = block.getElements('.rokquickcart-value-item'),
				key   = label.get('value'),
				name  = this.options.params + '[' + this.options.basename + '][' + key + ']',

				optionLabel, optionInput;

			label.set('name', name);

			opts.forEach(function(option, index){
				optionLabel = option.getElement('.rokquickcart-option');
				optionInput = option.getElement('input[type=text]');

				optionLabel.set('text', 'Option ' + (index + 1));
				optionInput.set('name', name + '[]');
			});
		},

		cleanBlocks: function(e){
			var blocks = this.container.getElements('.rokquickcart-field-block'), inputs;

			blocks.forEach(function(block){
				inputs = block.getElements('input[type=text]');
				if (!inputs[0].get('value') || !inputs.get('value').filter(function(item){ return item; }).length) this.remBlock(e, block.getElement('[data-rqc-remblock]'));
			}, this);
		}
	});

	this.RokQuickCartParams = RokQuickCartParams;


	var template =
'<div class="rokquickcart-field-block">' +
'		<div class="rokquickcart-key">' +
'			<input type="text" name="" placeholder="Nombre de categoría" value="">' +
'			<span class="btn" data-rqc-remblock=""><i class="icon-minus-2"></i></span>' +
'		</div>' +
'		<div class="rokquickcart-value" style="display: none;">' +
'		<div class="rokquickcart-value-item">' +
'			<span class="rokquickcart-option">Option 1</span>' +
'			<input type="text" name="" value="" class="input-small" placeholder="ie, Green, Blue, ...">' +
'			<span class="btn-group">' +
'				<span class="btn btn-mini" data-rqc-addrow=""><i class="icon-plus-2"></i></span>' +
'				<span class="btn btn-mini" data-rqc-remrow=""><i class="icon-minus-2"></i></span>' +
'			</span>' +
'		</div>' +
'		<div class="rokquickcart-value-item">' +
'			<span class="rokquickcart-option">Option 2</span>' +
'			<input type="text" name="" value="" class="input-small" placeholder="ie, Green, Blue, ...">' +
'			<span class="btn-group">' +
'				<span class="btn btn-mini" data-rqc-addrow=""><i class="icon-plus-2"></i></span>' +
'				<span class="btn btn-mini" data-rqc-remrow=""><i class="icon-minus-2"></i></span>' +
'			</span>' +
'		</div>' +
'		<div class="rokquickcart-value-item">' +
'			<span class="rokquickcart-option">Option 3</span>' +
'			<input type="text" name="" value="" class="input-small" placeholder="ie, Green, Blue, ...">' +
'			<span class="btn-group">' +
'				<span class="btn btn-mini" data-rqc-addrow=""><i class="icon-plus-2"></i></span>' +
'				<span class="btn btn-mini" data-rqc-remrow=""><i class="icon-minus-2"></i></span>' +
'			</span>' +
'		</div>' +
'		</div>' +
'</div>';

	var row =
'<div class="rokquickcart-value-item">' +
'	<span class="rokquickcart-option">Option X</span>' +
'	<input type="text" name="" value="" class="input-small" placeholder="ie, Green, Blue, ...">' +
'	<span class="btn-group">' +
'		<span class="btn btn-mini" data-rqc-addrow=""><i class="icon-plus-2"></i></span>' +
'		<span class="btn btn-mini" data-rqc-remrow=""><i class="icon-minus-2"></i></span>' +
'	</span>' +
'</div>';

})());
