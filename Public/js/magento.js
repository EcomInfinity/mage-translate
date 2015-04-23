'use strict';
var self = this;
if (Backbone.emulateJSON) {
    params.contentType = 'application/x-www-form-urlencoded';
    params.data = params.data ? {model: params.data} : {};
}

jQuery(function() {
    (function(){
        var root = this;
        var cms;
        cms = root.cms = {};
        var _ = root._,
        $ = root.jQuery;

        cms.Model = {};
        cms.Collection = {};
        cms.View = {};

        cms.Model.Base = Backbone.Model.extend({
            defaults:{
                'timestamp':-1
            },
            save: function(attributes, options) {
                $.fancybox.showLoading();
                var _success = options.success;
                options.success = function(resp) {
                    $.fancybox.hideLoading();
                    if (_success) _success(model, resp, options);
                };
                return Backbone.Model.prototype.save.call(this, attributes, options);
            }
        });

        var CmsRouter = Backbone.Router.extend({
            initialize: function(options){
                options || (options = {});
                this._cmsEvents = options._cmsEvents;
            },
            routes: {
                "cms": "cmsRender",
                "page": "pageRender",
                "block": "blockRender",
                "edit-page/:id\d": "eidtPage",
                "edit-block/:id\d": "editBlock",
            },

            cmsRender: function(){
                this._cmsEvents.trigger('refresh', 'page-view');
            },
            pageRender: function(){
                $('.block-cms-sidebar ul li').removeClass('menu-selection');
                $('.block-cms-sidebar ul li:eq(0)').addClass('menu-selection');
                this._cmsEvents.trigger('refresh', 'page-view');
                $('.block-cms-page').show();
                $('.block-cms-block').hide();
            },
            blockRender: function(){
                $('.block-cms-sidebar ul li').removeClass('menu-selection');
                $('.block-cms-sidebar ul li:eq(1)').addClass('menu-selection');
                this._cmsEvents.trigger('refresh', 'block-view');
                $('.block-cms-block').show();
                $('.block-cms-page').hide();
            },
            eidtPage: function (id){
                if(PurviewVal() == '-1' || Purview('update') == '1'){
                    this._cmsEvents.trigger('alernately',{cms_id:id},'storePage');
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                }
            },
            editBlock: function (id){
                if(PurviewVal() == '-1' || Purview('update') == '1'){
                    this._cmsEvents.trigger('alernately',{cms_id:id},'storeBlock');
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                }
            }
        });

        cms.View.CmsPageSearchView = Backbone.View.extend({
            template: _.template($('#tpl-cms-page-search').html()),
            events:{
                'keypress .page-search': 'searchPage',
                'focus .page-search': 'searchFocus',
                'click .search-clear': 'searchClear',
                'click .search-enter': 'searchEnter'
            },
            searchPage: function (event){
                if(event.keyCode == 13){
                    $(event.target).blur();
                    $('.search-enter').hide();
                    $('.search-clear').show();
                    var page_search = $(event.target).val();
                    this._cmsEvents.trigger('alernately',{page_search: page_search},'cmsPages');
                }
            },
            searchFocus: function (event){
                $('.search-enter').show();
                $('.search-clear').hide();
            },
            searchClear: function (event){
                $('.page-search').val('');
                $('.page-search').focus();
                this._cmsEvents.trigger('alernately',{page_search: ''},'cmsPages');
                $('.search-enter').show();
                $('.search-clear').hide();
                return false;
            },
            searchEnter: function (event){
                $('.search').blur();
                $('.search-enter').hide();
                $('.search-clear').show();
                this.search = $('.page-search').val();
                this._cmsEvents.trigger('alernately',{page_search: this.search},'cmsPages');
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
                // this.render();
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
                return this;
            }
        });

        cms.View.CmsPageListView = Backbone.View.extend({
            template: _.template($('#tpl-cms-page-list').html()),
            events:{
                'click .btn-page-translate': 'syncToTranslate',
                'click .btn-page-magento': 'syncToMagento',
                'change .batch-app': 'appPage',
                'click .btn-export-txt': 'exportContent'
            },
            syncToTranslate: function (event){
                if(PurviewVal() == '-1' || Purview('update') == '1' || Purview('create') == '1'){
                    this.cmsModel.clear();
                    $(event.target).closest('a').removeClass('btn-page-translate');
                    $('.btn-page-magento').removeClass('btn-block-magento');
                    var _self = this;
                    this.cmsModel.save({}, 
                        {url: UrlApi('_app')+'/create-page'}
                    ).done(function (response){
                        if (response.success === true) {
                            _self.$el.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            _self.render();
                        } else {
                            _self.$el.notify(
                                'Failure',
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            syncToMagento: function (event){
                if(PurviewVal() == '-1' || Purview('update') == '1'){
                    this.cmsModel.clear();
                    $(event.target).closest('a').removeClass('btn-page-magento');
                    $('.btn-page-translate').removeClass('btn-page-translate');
                    var _self = this;
                    this.cmsModel.save({}, 
                        {url: UrlApi('_app')+'/sync-all-page'}
                    ).done(function (response){
                        if (response.success === true) {
                            _self.$el.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            _self.render();
                        } else {
                            _self.$el.notify(
                                'Failure',
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            appPage: function (event){
                this.cmsModel.clear();
                var _self = this;
                this.data = {};
                this.operation = $(event.target).val();
                if($('.block-cms-page-list tbody input:checked').length < 1){
                    return;
                }
                $('.block-cms-page-list tbody input:checked').each(function (i){
                    _self.data[i] = $(this).val();
                });
                if(this.operation == 'download'){
                    if(PurviewVal() == '-1' || Purview('export') == '1'){
                        this.cmsModel.save(
                            {page_ids: this.data, type: 1},
                            {url: UrlApi('_app')+"/export-contents"}
                        ).done(function (response){
                            if(response.success === true){
                                _self.$el.notify(
                                    'Success',
                                    {
                                        position: 'top',
                                        className: 'success'
                                    }
                                );
                                window.open(UrlApi('_app')+'/download-contents');
                                $(event.target).find('option')[0].selected = true;
                                _self.render();
                            }else{
                                _self.$el.notify(
                                    'Failure',
                                    {
                                        position: 'top',
                                        className: 'error'
                                    }
                                );
                            }
                        });
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                // window.history.back();
                            }
                        });
                    }
                }
                if(this.operation == 'magento'){
                    if(PurviewVal() == '-1' || Purview('update') == '1'){
                        if(confirm('Are you sure update to magento?') == true){
                            this.cmsModel.save(
                                {page_ids: this.data},
                                {url: UrlApi('_app')+"/sync-checked-page"}
                            ).done(function (response){
                                if(response.success === true){
                                    _self.$el.notify(
                                        'Success',
                                        {
                                            position: 'top',
                                            className: 'success'
                                        }
                                    );
                                    $(event.target).find('option')[0].selected = true;
                                    _self.render();
                                }else{
                                    _self.$el.notify(
                                        'Failure',
                                        {
                                            position: 'top',
                                            className: 'error'
                                        }
                                    );
                                }
                            });
                        }else{
                            $(event.target).find('option')[0].selected = true;
                        }
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                // window.history.back();
                            }
                        });
                    }
                }
            },
            exportContent: function (event){
                if(PurviewVal() == '-1' || Purview('export') == '1'){
                    this.cmsModel.clear();
                    var cms_id = $(event.target).closest('tr').data("id");
                    this.cmsModel.save(
                        {cms_id: cms_id},
                        {url: UrlApi('_app')+"/export-content"}
                    ).done(function (response){
                        if(response.success === true){
                            window.open(UrlApi('_app')+'/download-content');
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            setCmsPageSerach: function(data){
                this.page_search = data.page_search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
                // this.render();
            },
            render: function(){
                this.cmsModel.clear();
                var _self = this;
                this.cmsModel.save(
                    {page_search: this.page_search},
                    {url:UrlApi('_app')+'/page-list'}
                ).done(function (response){
                    var data = {};
                    data['page_list'] = response.data.list;
                    data['pages_total'] = response.data.total;
                    data['pages_count'] = response.data.count;
                    _self.$el.html(_self.template(data));
                });
            }
        });

        cms.View.CmsPageView = Backbone.View.extend({
            template: _.template($('#tpl-store-cms-page').html()),
            events: {
                'click .btn-save-page': 'clickBtnPageSave'
            },
            _save: function(){
                this.cmsModel.clear();
                var _self = this;
                var $form = this.$el.find('form');
                this.cmsModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/page-save'}
                ).done(function (response){
                    if (response.success === true) {
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        _self._cmsEvents.trigger('refresh', 'page-view');
                    } else {
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
                // this.cmsModel.clear();
            },
            clickBtnPageSave: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
            },
            setStorePage: function(data){
                this.cms_id = data.cms_id;
                return this;
            },
            render: function(){
                this.cmsModel.clear();
                var _self = this;
                this.cmsModel.save(
                    {cms_id: this.cms_id},
                    { url: UrlApi('_app')+'/page-info' }
                ).done(function (response){
                    var data = {};
                    data['store_page'] = response.data;
                    _self.$el.html(_self.template(data));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._save.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el, {
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                // this.cmsModel.clear();
                return this;
            }
        });

        cms.View.CmsBlockSearchView = Backbone.View.extend({
            template: _.template($('#tpl-cms-block-search').html()),
            events:{
                'keypress .block-search': 'searchBlock',
                'focus .block-search': 'searchFocus',
                'click .search-clear': 'searchClear',
                'click .search-enter': 'searchEnter'
            },
            searchBlock: function (event){
                if(event.keyCode == 13){
                    $(event.target).blur();
                    $('.search-enter').hide();
                    $('.search-clear').show();
                    var block_search = $(event.target).val();
                    this._cmsEvents.trigger('alernately',{block_search: block_search},'cmsBlocks');
                }
            },
            searchFocus: function (event){
                $('.search-enter').show();
                $('.search-clear').hide();
            },
            searchClear: function (event){
                $('.block-search').val('');
                $('.block-search').focus();
                this._cmsEvents.trigger('alernately',{block_search: ''},'cmsBlocks');
                $('.search-enter').show();
                $('.search-clear').hide();
                return false;
            },
            searchEnter: function (event){
                $('.search').blur();
                $('.search-enter').hide();
                $('.search-clear').show();
                this.search = $('.block-search').val();
                this._cmsEvents.trigger('alernately',{block_search: this.search},'cmsBlocks');
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
            },
            render: function(){
                var data = {};
                this.$el.html(this.template(data));
                return this;
            }
        });

        cms.View.CmsBlockListView = Backbone.View.extend({
            template: _.template($('#tpl-cms-block-list').html()),
            events:{
                'click .btn-block-translate': 'syncToTranslate',
                'click .btn-block-magento': 'syncToMagento',
                'change .batch-app': 'appBlock',
                'click .btn-export-txt': 'exportContent'
            },
            syncToTranslate: function (event){
                if(PurviewVal() == '-1' || Purview('update') == '1' || Purview('create') == '1'){
                    this.cmsModel.clear();
                    $(event.target).closest('a').removeClass('btn-block-translate');
                    $('.btn-block-magento').removeClass('btn-block-magento');
                    var _self = this;
                    this.cmsModel.save({}, 
                        {url: UrlApi('_app')+'/create-block'}
                    ).done(function (response){
                        if (response.success === true) {
                            _self.$el.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            _self.render();
                        } else {
                            _self.$el.notify(
                                'Failure',
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            syncToMagento: function (event){
                if(PurviewVal() == '-1' || Purview('update') == '1'){
                    this.cmsModel.clear();
                    $(event.target).closest('a').removeClass('btn-block-magento');
                    $('.btn-block-translate').removeClass('btn-block-translate');
                    var _self = this;
                    this.cmsModel.save({}, 
                        {url: UrlApi('_app')+'/sync-all-block'}
                    ).done(function (response){
                        if (response.success === true) {
                            _self.$el.notify(
                                'Success',
                                {
                                    position: 'top',
                                    className: 'success'
                                }
                            );
                            _self.render();
                        } else {
                            _self.$el.notify(
                                'Failure',
                                {
                                    position: 'top',
                                    className: 'error'
                                }
                            );
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            appBlock: function (event){
                this.cmsModel.clear();
                var _self = this;
                this.data = {};
                this.operation = $(event.target).val();
                if($('.block-cms-block-list tbody input:checked').length < 1){
                    return;
                }
                $('.block-cms-block-list tbody input:checked').each(function (i){
                    _self.data[i] = $(this).val();
                });
                if(this.operation == 'download'){
                    if(PurviewVal() == '-1' || Purview('export') == '1'){
                        // console.log(this.data);
                        this.cmsModel.save(
                            {page_ids: this.data, type: 2},
                            {url: UrlApi('_app')+"/export-contents"}
                        ).done(function (response){
                            if(response.success === true){
                                _self.$el.notify(
                                    'Success',
                                    {
                                        position: 'top',
                                        className: 'success'
                                    }
                                );
                                window.open(UrlApi('_app')+'/download-contents');
                                $(event.target).find('option')[0].selected = true;
                                _self.render();
                            }else{
                                _self.$el.notify(
                                    'Failure',
                                    {
                                        position: 'top',
                                        className: 'error'
                                    }
                                );
                            }
                        });
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                // window.history.back();
                            }
                        });
                    }
                }

                if(this.operation == 'magento'){
                    if(PurviewVal() == '-1' || Purview('update') == '1'){
                        if(confirm('Are you sure update to magento?') == true){
                            this.cmsModel.save(
                                {block_ids: this.data},
                                {url: UrlApi('_app')+"/sync-checked-block"}
                            ).done(function (response){
                                if (response.success === true) {
                                    _self.$el.notify(
                                        'Success',
                                        {
                                            position: 'top',
                                            className: 'success'
                                        }
                                    );
                                    $(event.target).find('option')[0].selected = true;
                                    _self.render();
                                } else {
                                    _self.$el.notify(
                                        'Failure',
                                        {
                                            position: 'top',
                                            className: 'error'
                                        }
                                    );
                                }
                            });
                        }else{
                            $(event.target).find('option')[0].selected = true;
                        }
                    }else{
                        $.fancybox($('.message'),{
                           afterClose: function () {
                                // window.history.back();
                            }
                        });
                    }
                }
            },
            exportContent: function (event){
                if(PurviewVal() == '-1' || Purview('export') == '1'){
                    this.cmsModel.clear();
                    var cms_id = $(event.target).closest('tr').data("id");
                    this.cmsModel.save(
                        {cms_id: cms_id},
                        {url: UrlApi('_app')+"/export-content"}
                    ).done(function (response){
                        if(response.success === true){
                            window.open(UrlApi('_app')+'/download-content');
                        }
                    });
                }else{
                    $.fancybox($('.message'),{
                       afterClose: function () {
                            // window.history.back();
                        }
                    });
                }
                return false;
            },
            setCmsBlockSerach: function(data){
                this.block_search = data.block_search;
                return this;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
            },
            render: function(){
                this.cmsModel.clear();
                var _self = this;
                this.cmsModel.save(
                    {block_search: this.block_search},
                    {url:UrlApi('_app')+'/block-list'}
                ).done(function (response){
                    var data = {};
                    data['block_list'] = response.data.list;
                    data['blocks_total'] = response.data.total;
                    data['blocks_count'] = response.data.count;
                    _self.$el.html(_self.template(data));
                });
                return this;
            }
        });

        cms.View.CmsBlockView = Backbone.View.extend({
            template: _.template($('#tpl-store-cms-block').html()),
            events: {
                'click .btn-save-block': 'clickBtnBlockSave'
            },
            _save: function(){
                this.cmsModel.clear();
                var _self = this;
                var $form = this.$el.find('form');
                this.cmsModel.save(
                    $form.serializeObject(),
                    {url:UrlApi('_app')+'/block-save'}
                ).done(function (response){
                    if (response.success === true) {
                        $form.notify(
                            'Success',
                            {
                                position: 'top',
                                className: 'success'
                            }
                        );
                        _self._cmsEvents.trigger('refresh', 'block-view');
                    } else {
                        $form.notify(
                            response.message,
                            {
                                position: 'top',
                                className: 'error'
                            }
                        );
                    }
                });
            },
            clickBtnBlockSave: function(event){
                $(event.target).closest('form').submit();
                return false;
            },
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;
                this._cmsEvents = options._cmsEvents;
            },
            setStoreBlock: function(data){
                this.cms_id = data.cms_id;
                return this;
            },
            render: function(){
                this.cmsModel.clear();
                var _self = this;
                this.cmsModel.save(
                    {cms_id: this.cms_id},
                    { url: UrlApi('_app')+'/block-info' }
                ).done(function (response){
                    var data = {};
                    data['store_block'] = response.data;
                    _self.$el.html(_self.template(data));
                    _self.$el.find('form').validator().on('submit', function(e) {
                        if (e.isDefaultPrevented()) {
                        } else {
                            _self._save.call(_self);
                            return false;
                        }
                    });
                    $.fancybox(_self.$el, {
                       afterClose: function () {
                            window.history.back();
                        }
                    });
                });
                return this;
            }
        });

        cms.View.CmsApp = Backbone.View.extend({
            initialize: function(options){
                options || (options = {});
                this.cmsModel = options.cmsModel;

                var _cmsEvents = {};
                _.extend(_cmsEvents, Backbone.Events);

                var pagesearchView = new cms.View.CmsPageSearchView({
                    el: '.search-cms-page',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var blocksearchView = new cms.View.CmsBlockSearchView({
                    el: '.search-cms-block',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var pagelistView = new cms.View.CmsPageListView({
                    el: '.block-cms-page-list',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var blocklistView = new cms.View.CmsBlockListView({
                    el: '.block-cms-block-list',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var cmspageView = new cms.View.CmsPageView({
                    el: '.block-store-cms-page',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var cmsblockView = new cms.View.CmsBlockView({
                    el: '.block-store-cms-block',
                    cmsModel: this.cmsModel,
                    _cmsEvents: _cmsEvents
                });

                var router = new CmsRouter({
                    _cmsEvents: _cmsEvents
                });

                _cmsEvents.on('refresh', function(view) {
                    switch(view) {
                        case 'page-view':
                            pagesearchView.render();
                            pagelistView.render();
                            break;
                        case 'block-view':
                            blocksearchView.render();
                            blocklistView.render();
                            break;
                    }
                });
                _cmsEvents.on('alernately', function (data,view){
                    switch (view)
                    {
                        case 'storePage':
                            cmspageView.setStorePage(data).render();
                            break;
                        case 'cmsPages':
                            pagelistView.setCmsPageSerach(data).render();
                            break;
                        case 'storeBlock':
                            cmsblockView.setStoreBlock(data).render();
                            break;
                        case 'cmsBlocks':
                            blocklistView.setCmsBlockSerach(data).render();
                            break;
                    }
                });
            }
        });

        var cmsApp = new cms.View.CmsApp({
            cmsModel: new cms.Model.Base()
        });
        Backbone.history.start();

    }).call(self);

});
