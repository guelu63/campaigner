MODx.tree.CampaignerElement = function(config) {
        config = config || {};
        Ext.applyIf(config,{
            rootVisible: false
            ,title: ''
            // ,url: MODx.config.connectors_url+'element/index.php'
            ,url: MODx.config.connector_url
            ,action: 'element/getnodes'
            ,useDefaultToolbar: false
            ,menuConfig: []
            ,baseParams: {
                currentElement: MODx.request.id || 0
                ,currentAction: MODx.request.a || 0
            }
        });
        MODx.tree.CampaignerElement.superclass.constructor.call(this,config);
};
Ext.extend(MODx.tree.CampaignerElement,MODx.tree.Element);
Ext.reg('modx-tree-campaigner-element',MODx.tree.CampaignerElement);

MODx.tree.CampaignerPlaceholder = function(config) {
        config = config || {};
        Ext.applyIf(config,{
            url: Campaigner.config.connector_url
            ,action: 'mgr/newsletter/articles/getplaceholders'
            ,root_id: '0'
            ,root_name: _('campaigner.newsletter.edit.placeholder')
            ,menuConfig: []
            ,remoteToolbar: false
            ,rootVisible: true
            ,ddAppendOnly: false
            ,useDefaultToolbar: false
        });
        MODx.tree.CampaignerPlaceholder.superclass.constructor.call(this,config);
};
Ext.extend(MODx.tree.CampaignerPlaceholder,MODx.tree.Element);
Ext.reg('modx-tree-campaigner-placeholder',MODx.tree.CampaignerPlaceholder);


Campaigner.panel.NewsletterEditArticles = function(config) {
    config = config || {};
    // this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config, {
        id: 'campaigner-panel-newsletter-editarticles'
        // ,cls: 'container form-with-labels'
        ,defaults: { collapsible: false ,autoHeight: true }
        ,forceLayout: true
        ,forceFit: true
        ,buttons: [{
            text: _('campaigner.save')
        },{
            text: _('campaigner.back')
        }]
        ,items: [{
            xtype: 'container'
            ,layout: 'column'
            ,items: [{
                layout: 'form'
                ,border: false
                ,labelAlign: 'top'
                ,columnWidth: .75
                // ,id: 'campaigner-newsletter-edit-content'
                ,items: [{
                    xtype: 'textarea'
                    ,allowDrop: true
                    ,enableDD: true
                    ,ddGroup: 'modx-treedrop-dd'

                    ,id: 'campaigner-newsletter-edit-content'
                    // ,cls: 'modx-richtext'
                    ,html: true
                    ,height: 600
                    ,listeners: {
                        'render': {fn: this.getContent, scope: this}
                    },
                }, {
                    xtype: 'textfield'
                }]
            },{
                layout: 'form'
                ,labelAlign: 'top'
                ,border: false
                ,columnWidth: .25
                ,items: [{
                    xtype: 'modx-combo'
                    ,anchor: '100%'
                    ,url: Campaigner.config.connector_url
                    ,baseParams: {
                        action: 'mgr/subscriber/getlist'
                    }
                    ,fields: ['email']
                    ,valueField: 'email'
                    ,displayField: 'email'
                    ,emptyText: 'E-Mail'
                    ,fieldLabel: _('campaigner.subscriber.email')
                    // ,cls: 'modx-combo'
                    ,name: 'email'
                    ,listeners: {
                        'select': {fn: this.getContent, scope: this}
                        // ,'change': {fn this.triggerFilter, scope: this}
                    }
                }
                ,{
                    xtype: 'panel'
                    ,html: '<div id="file-upload" />'+_('gallery.loading_ellipsis')+'</div>'
                    ,id: 'file-upload-field'
                    ,border: false
                }
                // ,{
                //     xtype: 'hidden'
                //     ,name: 'attachments'
                //     ,fieldLabel: _('campaigner.newsletter.edit.attachments')
                //     ,anchor: '100%'
                // },{
                //     xtype: 'panel'
                //     ,border: false
                //     ,id: 'campaigner-newsletter-edit-attachment-list'
                // },{
                //     xtype: 'modx-combo-browser'
                //     ,name: 'add_attachment'
                //     ,anchor: '100%'
                //     ,listeners: {
                //         'select': {fn: this.addAttachment, scope: this}
                //     }
                // }
                ,{
                    xtype: 'modx-tree-campaigner-placeholder'
                    // ,listeners: {
                    //     // 'afterrender': function(tree) {
                    //     //     tree.getTopToolbar().hide();
                    //     // }
                    //     'dragdrop': function( t, node, dd, e ) {
                    //         console.log(arguments);
                    //     }
                    // }
                }]
            }]
        }]
        ,listeners: {
            'activate': function(w,e) {
                MODx.loadRTE('campaigner-newsletter-edit-content');
            }
            ,'deactivate': function(w,e) {
                // tinyMCE.execCommand('mceRemoveControl',true,'campaigner-newsletter-edit-content');
            }
        }

    });
    Campaigner.panel.NewsletterEditArticles.superclass.constructor.call(this,config);
    this.on('show',this.setup,this);
}
Ext.extend(Campaigner.panel.NewsletterEditArticles, MODx.FormPanel
    ,{
        setup: function() {
            var results = {};
            this.fireEvent('ready');

            // var template = new Ext.template({
            //     x
            // });

            if (typeof Campaigner.panel.NewsletterEditArticles.uploader == 'undefined') {
                Campaigner.panel.NewsletterEditArticles.uploader = new qq.FileUploader({
                    element: document.getElementById('file-upload')
                    // ,debug: true
                    ,maxConnections: 50
                    ,template: '<div id="campaigner-uploader" class="campaigner-uploader qq-uploader">' +
                        '<div class="qq-upload-area"><div class="qq-upload-drop-area"><span>Dateien hier loslassen!</span></div>' +
                        '<div class="qq-upload-button">Dateien hierher ziehen oder klicken</div></div>' +
                        '<p class="qq-upload-actions"><a href="#" onclick="clearSuccess(); return false;">Erfolgreiche leeren</a> ' +
                        '<a href="#" onclick="clearFailure(); return false;">Fehlerhafte Leeren</a></p>' +
                        '<ul class="qq-upload-list"></ul>' +
                        '</div>'
                    ,fileTemplate: '<li>' +
                        '<span class="qq-upload-spinner"></span>' +
                        '<a class="qq-upload-cancel" href="#">Abbrechen</a>' +
                        '<span class="qq-upload-size"></span>' +
                        '<span class="qq-upload-file"></span>' +
                        '<span class="qq-upload-failed-text">Fehler</span>' +
                        '</li>'
                    ,action: Campaigner.config.connector_url
                    ,params: {
                        action: 'mgr/item/ajaxupload'
                        ,HTTP_MODAUTH: MODx.siteId
                    }
                    ,onComplete: function(id, fileName, result) {
                        results[id] = result;
                        Campaigner.panel.NewsletterEditArticles.uploader.fireEvent('success');
                        // Fire complete when results length equals list child elements count
                        // That means: All files were uploaded successfully
                        if(Campaigner.panel.NewsletterEditArticles.uploader._listElement.childElementCount == Object.keys(results).length)
                            Campaigner.panel.NewsletterEditArticles.uploader.fireEvent('uploadcomplete', results);
                    }
                    ,onSubmit: function() {
                        var nd = Campaigner.panel.NewsletterEditArticles.uploader.config.record;
                        // var f = Campaigner.panel.NewsletterEditArticles.getForm();
                        // var data = {
                        //     path: nd.path
                        //     ,pathRelative: nd.pathRelative
                        //     ,perms: nd.perms
                        //     ,type: nd.type
                        //     ,source: nd.loader.baseParams.source
                        //     ,wctx: nd.loader.baseParams.wctx
                        // };
                        // var p = this.params;
                        // Ext.apply(p, data);
                        // Campaigner.panel.NewsletterEditArticles.uploader.setParams(p);
                    }
                });
                Campaigner.panel.NewsletterEditArticles.uploader = this;
            }
        }
        ,addAttachment: function(data) {
            if(!Campaigner.attachments)
                Campaigner.attachments = [];

            // var attachments = Ext.getCmp('campaigner-panel-newsletter-editarticles').getForm().findField('attachments');
            // var already = attachments.getValue().split(',');
            // already.push(data.relativeUrl);
            // attachments.setValue(already.join(','));
            // var panel = new Ext.Panel({
            //     border: false
            //     ,html: '<img src="'+data.image+'" /> '+data.relativeUrl
            // });

            // console.log(Campaigner.attachments);
            // console.log(data);
            var array = [data.image, data.pathName, data.cls];
            Campaigner.attachments.push(array);

            var store = new Ext.data.ArrayStore({
                data: Campaigner.attachments
                ,fields: [
                    'image', 'pathName', 'cls'
                ]
            });
            var listview = Ext.getCmp('campaigner-newsletter-edit-attachment-list-view');

            if(listview) {
                listview.getView().refresh();
                return;
            }

            // store.load(data);

            var listView = new Ext.list.ListView({
                id: 'campaigner-newsletter-edit-attachment-list-view',
                store: store,
                multiSelect: true,
                emptyText: 'No images to display',
                reserveScrollOffset: true,
                columns: [{
                    header: 'File',
                    width: .5,
                    dataIndex: 'pathName'
                },{
                    header: 'Last Modified',
                    width: .35,
                    dataIndex: 'image',
                    // tpl: '{lastmod:date("m-d h:i a")}'
                },{
                    header: 'Size',
                    dataIndex: 'cls',
                    // tpl: '{size:fileSize}', // format using Ext.util.Format.fileSize()
                    align: 'right'
                }]
            });



            Ext.getCmp('campaigner-newsletter-edit-attachment-list').add(listView);
            Ext.getCmp('campaigner-newsletter-edit-attachment-list').doLayout();
            // console.log(tpl.applyTemplate(data));
        }
        ,activate: function(){

            if(!this.config.newsletter)
                return;

            MODx.Ajax.request({
                url: Campaigner.config.connector_url
                ,params: {
                    action: 'mgr/newsletter/articles/getcontent'
                    ,newsletter: this.config.newsletter
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        // console.log(r);
                        Ext.getCmp('campaigner-newsletter-edit-content').setValue(r.object.html);
                        tinyMCE.get('campaigner-newsletter-edit-content').setContent(r.object.html);
                    },scope:this}
                }
            });
            Ext.getCmp('card-container').getLayout().setActiveItem(this.id);
        }
        ,editLetter: function(btn, e) {
            location.href = MODx.config.manager_url + 'index.php?a=30&id='+ this.config.params.docid +'&letter=1';
            return;
        }
        ,getContent: function(el) {
            MODx.Ajax.request({
                url: Campaigner.config.connector_url
                ,params: {
                    action: 'mgr/newsletter/articles/getcontent'
                    ,newsletter: this.config.newsletter
                    ,email: el.getValue()
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        // console.log(r);
                        // Ext.getCmp('campaigner-newsletter-edit-content').setValue(r.object.html);
                        tinyMCE.get('campaigner-newsletter-edit-content').setContent(r.object.html);
                    },scope:this}
                }
            });
        }
        ,hideWait: function() {
            this.wait.hide();
        }
        ,addSection: function(btn, e) {

            var form = Ext.getCmp('campaigner-newsletter-form-articles');
            // var fieldset = btn.ownerCt.ownerCt;
            form.add({
                xtype: 'fieldset'
                ,title: 'Section'
                ,width: '100%'
                ,collapsible: true
                ,defaultType: 'textfield'
                ,defaults: {
                    anchor: '100%'
                }
                ,layout: 'column'
                ,items: [{
                    xtype: 'textfield'
                    ,columnWidth: .5
                },{
                    xtype: 'textfield'
                    ,enableDD: true
                    ,ddGroup: 'modx-treedrop-dd'
                    ,columnWidth: .5
                }]
            });
            form.doLayout();
            // var form = Ext.getCmp('campaigner-newsletter-form-articles');
        }
    }
);
Ext.reg('campaigner-panel-newsletter-editarticles', Campaigner.panel.NewsletterEditArticles);

// Campaigner.panel.NewsletterArticles = function(config) {
//     config = config || {};
//     this.ident = config.ident || 'campaigner-'+Ext.id();
//     // this.sm = new Ext.grid.CheckboxSelectionModel();
//     Ext.applyIf(config,{
//         id: 'campaigner-newsletter-form-articles'
//         ,anchor: '100%'
//         ,width: '100%'
//         ,url: Campaigner.config.connector_url
//         // ,enableDD: true
//         // ,ddGroup: 'modx-treedrop-dd'
//         // ,items: [{
//         //     xtype: 'textfield'
//         //     ,name: 'resources'
//         //     ,listeners: {
//         //         'render': function() {
//         //             // console.log(this.getEl());
//         //             // this.getEl().set({data-type': 'modResource'});
//         //         }
//         //     }
//         // }]
//     });
//     Campaigner.panel.NewsletterArticles.superclass.constructor.call(this,config);
// }

// Ext.extend(Campaigner.panel.NewsletterArticles, MODx.FormPanel
//     ,{
//         constructor: function(cfg) {
//             this.initConfig(cfg);
//         }
//     }
// );
// Ext.reg('campaigner-newsletter-form-articles', Campaigner.panel.NewsletterArticles);

Campaigner.grid.NewsletterGridArticles = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    // this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'campaigner-newsletter-grid-articles'
        ,url: Campaigner.config.connector_url
        ,baseParams: {
            action: 'mgr/newsletter/articles/getlist'
        }
        ,enableDragDrop: true
        // ,plugins: {
        //     ptype: 'gridviewdragdrop',
        //     dragGroup: 'secondGridDDGroup',
        //     dropGroup: 'firstGridDDGroup'
        // }
        ,fields: ['id', 'pagetitle', 'section']
        ,paging: true
        ,pageSize: 10
        ,grouping: true
        ,groupBy: 'section'
        // ,view: new Ext.grid.GroupingView({
        //     forceFit:true
        //     ,showGroupName:true
        //     ,groupTextTpl: '{[values.rs[0].data["section"]}'
        // })
        // ,groupRenderer: function(value, p, rec) {
        //     // console.log(rec);
        //     return rec.data.section;
        // }
        ,collapseFirst: false
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        // ,sm: this.sm
        ,columns: [
            // this.sm,
        {
            header: _('campaigner.newsletter.articles.id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 10
        }
        ,{
            header: _('campaigner.newsletter.articles.pagetitle')
            ,dataIndex: 'pagetitle'
            ,sortable: true
            ,width: 40
        }
        ,{
            header: _('campaigner.newsletter.articles.section')
            ,dataIndex: 'section'
            ,hidden: true
        }]
        ,listeners: {
            drop: function(node, data, dropRec, dropPosition) {
                console.log(node);
            }
        }
        /**, tbar: [{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'campaigner-filter-search-group-subscribers'
            ,emptyText: _('campaigner.subscriber.search')+'...'
            ,listeners: {
                'change': {fn: this.filterSearch, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;
                        }, scope: cmp
                    });
                },scope:this}
            }
        }]*/
    });
    // this.view = new Ext.grid.GroupingView({
    //     emptyText: config.emptyText || _('ext_emptymsg')
    //     ,forceFit: true
    //     ,autoFill: true
    //     ,showPreview: true
    //     ,enableRowBody: true
    //     ,scrollOffset: 0
    //     ,groupTextTpl: '<div>{section}</div>'
    // });
    Campaigner.grid.NewsletterGridArticles.superclass.constructor.call(this,config);
}
Ext.extend(Campaigner.grid.NewsletterGridArticles, MODx.grid.Grid
    ,{
        constructor: function(cfg) {
            this.initConfig(cfg);
        }
    }
);
Ext.reg('campaigner-newsletter-grid-articles', Campaigner.grid.NewsletterGridArticles);
