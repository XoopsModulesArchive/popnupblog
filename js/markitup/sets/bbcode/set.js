// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	//previewParserPath:	'post.php', // path to your BBCode parser
	markupSet: [
		{name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]', className:'newbb-markitup-bold'},
		{name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]', className:'newbb-markitup-italic'},
		{name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]', className:'newbb-markitup-underline'},
		{name:'Delete', key:'D', openWith:'[d]', closeWith:'[/d]', className:'newbb-markitup-delete'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]', className:'newbb-markitup-picture'},
		{name:'Link', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...', className:'newbb-markitup-link'},
		{separator:'---------------' },
		{name:'Colors', openWith:'[color=[![Color]!]]', closeWith:'[/color]', className:'newbb-markitup-color', dropMenu: [
			{name:'Butter', openWith:'[color=FCE94F]', closeWith:'[/color]', className:"col1-1" },
			{name:'Butter', openWith:'[color=EDD400]', closeWith:'[/color]', className:"col1-2" },
			{name:'Butter', openWith:'[color=C4A000]', closeWith:'[/color]', className:"col1-3" },
			
			{name:'Orange', openWith:'[color=FCAF3E]', closeWith:'[/color]', className:"col2-1" },
			{name:'Orange', openWith:'[color=F57900]', closeWith:'[/color]', className:"col2-2" },
			{name:'Orange', openWith:'[color=CE5C00]', closeWith:'[/color]', className:"col2-3" },
			
			{name:'Chocolate', openWith:'[color=E9B96E]', closeWith:'[/color]', className:"col3-1" },
			{name:'Chocolate', openWith:'[color=C17D11]', closeWith:'[/color]', className:"col3-2" },
			{name:'Chocolate', openWith:'[color=8F5902]', closeWith:'[/color]', className:"col3-3" },
			
			{name:'Chameleon', openWith:'[color=8AE234]', closeWith:'[/color]', className:"col4-1" },
			{name:'Chameleon', openWith:'[color=73D216]', closeWith:'[/color]', className:"col4-2" },
			{name:'Chameleon', openWith:'[color=4E9A06]', closeWith:'[/color]', className:"col4-3" },
			
			{name:'Sky Blue', openWith:'[color=729FCF]', closeWith:'[/color]', className:"col5-1" },
			{name:'Sky Blue', openWith:'[color=3465A4]', closeWith:'[/color]', className:"col5-2" },
			{name:'Sky Blue', openWith:'[color=204A87]', closeWith:'[/color]', className:"col5-3" },
			
			{name:'Plum', openWith:'[color=AD7FA8]', closeWith:'[/color]', className:"col6-1" },
			{name:'Plum', openWith:'[color=75507B]', closeWith:'[/color]', className:"col6-2" },
			{name:'Plum', openWith:'[color=5C3566]', closeWith:'[/color]', className:"col6-3" },
			
			{name:'Scarlet Red', openWith:'[color=EF2929]', closeWith:'[/color]', className:"col7-1" },
			{name:'Scarlet Red', openWith:'[color=CC0000]', closeWith:'[/color]', className:"col7-2" },
			{name:'Scarlet Red', openWith:'[color=A40000]', closeWith:'[/color]', className:"col7-3" },
			
			{name:'Aluminium', openWith:'[color=888A85]', closeWith:'[/color]', className:"col8-1" },
			{name:'Aluminium', openWith:'[color=555753]', closeWith:'[/color]', className:"col8-2" },
			{name:'Aluminium', openWith:'[color=000000]', closeWith:'[/color]', className:"col8-3" },
		]},
		{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', className:'newbb-markitup-size', closeWith:'[/size]',
			dropMenu :[
				{name:'Small', openWith:'[size=small]', closeWith:'[/size]' },
				{name:'Medium', openWith:'[size=medium]', closeWith:'[/size]' },
				{name:'Large', openWith:'[size=large]', closeWith:'[/size]' },
				{name:'X-Large', openWith:'[size=x-large]', closeWith:'[/size]' },
				{name:'XX-Large', openWith:'[size=xx-large]', closeWith:'[/size]' }
			]},
		//{separator:'---------------' },
		//{name:'Bulleted list', openWith:'[list]\n', closeWith:'\n[/list]'},
		//{name:'Numeric list', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		//{name:'List item', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]', className:'newbb-markitup-quote'},
		{name:'Code', openWith:'[code]', closeWith:'[/code]', className:'newbb-markitup-code'}, 
		{separator:'---------------' },
		{name:'Clean', className:"newbb-markitup-clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		//{name:'Preview', className:"preview", call:'preview' }
	]
}