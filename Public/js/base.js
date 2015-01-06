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

			lang.View.LanguageAddView = Backbone.View.extend({
				template: _.template($('#tpl-lang-add').html()),
				events: {
					'click .btn-add': 'addLanguage'
				},
				addLanguage: function(event){
					var _self = this;
					var $form=$(event.target).closest('form');
					this.data_form = $form.serializeObject();
					console.log(this.data_form);
					translate.save(this.data_form,
						{url:'Translation/lang_add'}
						).done(function (response){
							console.log(response);
							_self.listView.render();
						});
				},
				initialize: function(options){
					options || (options = {});
					this.listView = options.listView;
					this.render();
				},
				render: function(){
					var data = {};
					this.$el.html(this.template(data));
				}
			});

			lang.View.LanguageSearchView = Backbone.View.extend({
				template: _.template($('#tpl-lang-search').html()),
				events:{
					'keypress .search': 'searchLanguage'
				},
				searchLanguage: function(event){
					if(event.keyCode == '13'){
						this.search = $(event.target).val();
						// console.log(this.search);
						this.inrender = '1';
						this.listView.setList(this.search,this.inrender).render();
					}
				},
				initialize:function(options){
					options || (options = {});
					this.listView = options.listView;
					this.render();
				},
				render: function(){
					var data = {};
					this.$el.html(this.template(data));
				}
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
					var _self = this;
					this.del_id = $(event.target).closest('tr').data('id');
					translate.save({id:this.del_id},
						{url:'Translation/lang_del'}
						).done(function (response){
							_self.render();
					});
				},
				setList: function(search,inrender){
					this.search = search;
					this.inrender = inrender;
					return this;
				},
				initialize: function(options){
					options || (options = {});
					this.lists = options.lists;
					this.editView = options.editView;
					this.inrender = '0';
					this.render();
				},
				render: function(){
					var _self = this;
					var data = {};
					translate.save({search:this.search,inrender:this.inrender},
						{url:'Translation/lang_list'}
						).done(function (response){
						data['lists'] = response;
						_self.$el.html(_self.template(data));
						
					});	
				}
			});

			lang.View.LanguageEditView = Backbone.View.extend({
				template: _.template($('#tpl-lang-edit').html()),
				events:{
					'change .lang_edit': 'editInfo',
					'click .btn-image-delete': 'imgageDel',
					'change #images': 'imagesAdd'
				},
				editInfo: function(event){
					var _change = $(event.target);
					this.langType = _change.attr("lang_type");
					this.langInfo = _change.val();
					this.langId = _change.closest('.form-holder').data("id");
					translate.save({langId:this.langId,langInfo:this.langInfo,langType:this.langType},
						{url:'Translation/lang_edit_info'}
						).done(function (response){	
						console.log(response);					
					});	
				},
				imgageDel: function(event){
					var _self = this;
					var _click = $(event.target);
					this.imageId = _click.attr('image-id');
					this.imageName = _click.attr('image-name');
					translate.save({imageId:this.imageId,imageName:this.imageName},
						{url:'Translation/lang_image_del'}
						).done(function (response){
						_self.render();
					});
				},
				imagesAdd: function(event){
					this.langId = $(event.target).closest('.form-holder').data("id");
					console.log(this.langId);
					var _self = this;
					ajaxFileUpload(
						'Translation/lang_image_add/lang_id/'+this.langId,
						function() {
							// _self;
							_self.render();
						}, 
						function() {
							alert('Add Fail');
						}
					);
				},
				initialize: function(options){
					options || (options = {});
					//this.render();
				},
				setLanguage: function(langId){
					this.langId = parseInt(langId);
					// console.log(this.langId);
					return this;
				},
				render: function(){
					var _self = this;
					var data = {};
					translate.save({id:this.langId},
						{url:'Translation/lang_edit_detail'}
						).done(function (response){
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
            			el: '.block-translation-detail'
            		});

            		this.listView = new lang.View.LanguageListView({
            			el: '.block-translation-list',
            			editView: this.editView
            		});

					this.addView = new lang.View.LanguageAddView({
						el: '.block-translation-add',
						listView: this.listView
					});

		            this.searchView = new lang.View.LanguageSearchView({
						el: '.search-box',
						listView: this.listView
					});
            	}
            });

            var translation = new lang.View.TranslationLang({});

		}).call(self);

	}); 