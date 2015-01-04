'use strict';
var self = this;
if (Backbone.emulateJSON) {  
      params.contentType = 'application/x-www-form-urlencoded';  
      params.data = params.data ? {model: params.data} : {};  
    }  
jQuery(function() {
		(function(){
			var root = this;
			var lang;
			lang = root.lang = {};
			var _ = root._,
			$ = root.jQuery;

			lang.Model = {};
			lang.Collection = {};
			lang.View = {};

			lang.Model.Base = Backbone.Model.extend({
				defaults:{
					'timestamp':-1
				}
			});
lang.Model.Test = Backbone.Model.extend({
	url:"Translation/translation_test",
	// initialize: function(){
	// 	this.bind("change:data",function(){
	// 		var name = this.get("data");
	// 		alert(JSON.stringify(name));
	// 	});
	// 	this.bind("error",function(model,error){
	// 		alert(JSON.stringify(error));
	// 	});
	// },
	defaults:{
		id:-1
	}
});
var man = new lang.Model.Test();



			lang.Model.Language = lang.Model.Base.extend({
				constructor: function(){
					Backbone.Model.apply(this,arguments);
				}
			});

			 lang.Collection.LangList = Backbone.Collection.extend({
				model: lang.Model.Language,
				localStorage: new Backbone.LocalStorage('lang.langlocator,langs'),
			});

			lang.View.LanguageEditView = Backbone.View.extend({
				template: _.template($('#tpl-lang-edit').html()),
				events:{
					'click #btn-test': 'editLanguage'
				},
				editLanguage: function(event){
					this.edit_id = $(event.target).closest('tr').data('id');
				},
				initialize: function(options){
					options || (options = {});
					this.render();
				},
				render: function(){
					var data = {a:'1'};
					this.$el.html(this.template(data));
				}
			});

			lang.View.LanguageListView = Backbone.View.extend({
				template: _.template($('#tpl-lang-list').html()),
				events:{
					'click .btn-edit': 'editLanguage'
				},
				editLanguage: function(event){
					this.edit_id = $(event.target).closest('tr').data('id');
					console.log(this.edit_id);
				},
				initialize: function(options){
					options || (options = {});
					this.lists = options.lists;
					this.render();
				},
				render: function(){
					var data = {};
					data['lists'] = this.lists;
					console.log(this.lists);
					this.$el.html(this.template(data));
				}
			});

            lang.View.TranslationLang = Backbone.View.extend({
            	initialize: function(options){
            		options || (options = {});

            		this.editView = new lang.View.LanguageEditView({
            			el: '.block-edit'
            		});
					man.save({},{success:function(model,response){  
		                //console.log(response);
		                this.listView  = new lang.View.LanguageListView({
							el: '.block-list',
							lists: response,
							//editView: this.editView
						});
		            }});
            	}
            });

            var translation = new lang.View.TranslationLang({});

		}).call(self);

	}); 