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

			var translate = new lang.Model.Base();



			lang.Model.Language = lang.Model.Base.extend({
				constructor: function(){
					Backbone.Model.apply(this,arguments);
				}
			});

			 lang.Collection.LangList = Backbone.Collection.extend({
				model: lang.Model.Language,
				localStorage: new Backbone.LocalStorage('lang.langlocator,langs'),
			});

			lang.View.LanguageListView = Backbone.View.extend({
				template: _.template($('#tpl-lang-list').html()),
				events:{
					'click .btn-edit': 'editLanguage',
					'click .btn-delete': 'deleteLanguage'
				},
				editLanguage: function(event){
					this.edit_id = $(event.target).closest('tr').data('id');
					this.editView.setLanguage(this.edit_id).render();
				},
				deleteLanguage: function(event){
					this.del_id = $(event.target).closest('tr').data('id');
					translate.save({id:this.del_id},{url:'Translation/translation_del'}).done(function (response){
						console.log(response);
					});
				},
				initialize: function(options){
					options || (options = {});
					this.lists = options.lists;
					this.editView = options.editView;
					this.render();
				},
				render: function(){
					var data = {};
					data['lists'] = this.lists;
					// console.log(this.lists);
					this.$el.html(this.template(data));
				}
			});

			lang.View.LanguageEditView = Backbone.View.extend({
				template: _.template($('#tpl-lang-edit').html()),
				events:{
					'change .lang_edit': 'editLanguage',
				},
				editLanguage: function(event){
					var _change = $(event.target);
					this.langType = _change.attr("lang_type");
					this.langInfo = _change.val();
					this.langId = _change.closest('.form-holder').data("id");
					translate.save({langType:this.langType,langInfo:this.langInfo,langId:this.langId},{url:'Translation/translation_edit'}).done(function (response){
						console.log(response);
					});
				},
				initialize: function(options){
					options || (options = {});
					//this.render();
				},
				setLanguage: function(langId){
					// langTd || (langId = []);
					this.langId = parseInt(langId);
					// console.log(this.langId);
					return this;
				},
				render: function(){
					var _self = this;
					var data = {};
					translate.save({id:this.langId},{url:'Translation/translation_edit'}).done(function (response){
						data['langDetail'] = response.detail;
						data['langImages'] = response.images;
						_self.$el.html(_self.template(data));
						
					});		
					
				}
			});

            lang.View.TranslationLang = Backbone.View.extend({
            	initialize: function(options){
            		var _self = this;
            		options || (options = {});

            		this.editView = new lang.View.LanguageEditView({
            			el: '.block-edit'
            		});

		            translate.save({},{url:'Translation/translation_test'}).done(function (response){
		                //console.log(response);
		                _self.listView  = new lang.View.LanguageListView({
							el: '.block-list',
							lists: response,
							editView: _self.editView
						});

		            });
            	}
            });

            var translation = new lang.View.TranslationLang({});

		}).call(self);

	}); 