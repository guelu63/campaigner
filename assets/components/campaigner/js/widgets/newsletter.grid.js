Campaigner.grid.Newsletter = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: {
            action: 'mgr/newsletter/getList'
        }
        ,fields: ['id', 'docid', 'state', 'sent_date', 'auto', 'nl_count', 'total', 'sent', 'bounced', 'sender', 'sender_email', 'subject', 'date', 'groups', 'priority']
        ,paging: true
        ,grouping: true
        ,groupBy: 'auto'
        ,view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:true,
            groupTextTpl: '{[values.rs[0].data["auto"] ? "Auto-Newsletter: " : ""]}{[ values.rs[0].data["subject"] ]}'
        })
        // ,groupRenderer: function(value, p, rec) {
        //     // console.log(rec);
        //     return rec.data.subject + ' (' + rec.data.auto + ')';
        // }
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,sm: this.sm
        ,columns: [
            this.sm,
        {
            header: _('campaigner.newsletter.id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 40
            // ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.newsletter.subject')
            ,dataIndex: 'subject'
            ,sortable: true
            ,width: 40
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.newsletter.auto')
            ,dataIndex: 'auto'
            ,hidden: true
            // ,sortable: true
            // ,width: 20
        },{
            header: _('campaigner.newsletter.sender')
            ,dataIndex: 'sender'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderSender
        },{
            header: _('campaigner.newsletter.date')
            ,dataIndex: 'date'
            ,sortable: true
            ,width: 12
            ,renderer: this._renderDate
        },{
            header: _('campaigner.newsletter.state')
            ,dataIndex: 'state'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderState
        },{
            header: _('campaigner.newsletter.groups')
            ,dataIndex: 'groups'
            ,sortable: true
            ,width: 35
            ,renderer: this._renderGroups
        },{
            header: _('campaigner.newsletter.priority')
            ,dataIndex: 'priority'
            ,sortable: true
            ,width: 5
        },{
            header: _('campaigner.newsletter.sentdate')
            ,dataIndex: 'sent_date'
            ,sortable: true
            ,width: 12
        },{
            header: _('campaigner.newsletter.total')
            ,dataIndex: 'total'
            ,sortable: true
            ,width: 8
        },{
            header: _('campaigner.newsletter.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderCount
        },{
            header: _('campaigner.newsletter.bounced')
            ,dataIndex: 'bounced'
            ,sortable: true
            ,width: 8
            ,renderer: this._renderCount
        }],
        /* Top toolbar */
        tbar : [{
            xtype: 'splitbutton'
            ,hidden: MODx.perm.newsletter_remove ? false : true
            ,text: _('campaigner.newsletter.batch_actions')
            ,menu: {
                items: [
                {
                    text: _('campaigner.newsletter.batch_remove')
                    ,handler: this.removeNewsletter
                    ,scope: this
                }]
            }
        }, '-', {
            xtype: 'combo'
            ,name: 'sent'
            ,id: 'campaigner-filter-sent'
            ,width: 150
            ,store: [
                ['-', _('campaigner.all')],
                [1, _('campaigner.newsletter.sent')],
                [0, _('campaigner.newsletter.scheduled')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'sent'
            ,submitValue: false
            ,emptyText: _('campaigner.newsletter.filter.sent')
            ,listeners: {
                'change': {fn: this.filterSent, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        },{
            xtype: 'combo'
            ,name: 'state'
            ,id: 'campaigner-filter-state'
            ,width: 150
            ,store: [
                ['-', _('campaigner.all')],
                [1, _('campaigner.newsletter.approved')],
                [0, _('campaigner.newsletter.unapproved')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'state'
            ,submitValue: false
            ,emptyText: _('campaigner.newsletter.filter.state')
            ,listeners: {
                'change': {fn: this.filterState, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        },'->',{
            xtype: 'button'
            ,id: 'campaigner-filter-mediainfo'
            ,text: 'nur Mediainfo'
            ,listeners: {
                'click': {fn: this.toggleMedia, scope: this}
            }
        },{
            xtype: 'button'
            ,id: 'campaigner-filter-autonewsletter'
            ,text: 'ohne Auto-Newsletter'
            ,listeners: {
              'click': {fn: this.toggleAuto, scope: this}
            }
        }
        ,{
            xtype: 'splitbutton'
            ,hidden: MODx.perm.newsletter_clearing ? false : true
            ,text: 'Alles bereinigen'
            ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 1}])
            // ,tooltip: {text:'This is a an example QuickTip for a toolbar item', title:'Tip Title'}
            // Menus can be built/referenced by using nested menu config objects
            ,menu : {
                items: [{
                    text: 'Entrümpeln'
                    ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 'trash'}], true)
                    ,scope : this
                }, {
                    text: 'Archivieren'
                    ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 'archiver'}], true)
                    ,scope : this
                }, '-', {
                    text: 'HTML aus Content entfernen'
                    ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 'dehtml'}], true)
                    ,scope : this
                }]
            }
        }]
    });
    Campaigner.grid.Newsletter.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Newsletter,MODx.grid.Grid,{
    filterState: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.state;
        } else {
                s.baseParams.state = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,filterSent: function(tf,newValue,oldValue) {
        var nv = newValue;
        var s = this.getStore();
        if(nv == '-') {
            delete s.baseParams.sent;
        } else {
            s.baseParams.sent = nv;
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,toggleMedia: function(btn, e) {
        var s = this.getStore();
        if (btn.text == 'nur Mediainfo') {
            s.setBaseParam('media',1);
            btn.setText('Alle');
        } else {
            s.setBaseParam('media',0);
            btn.setText('nur Mediainfo');
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,cleanerMedia: function(item, e, args) {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/archive',
                cleaner: args.cleaner
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getBottomToolbar().changePage(1);
                    this.refresh();
                },scope:this}
            }
        });
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,toggleAuto: function(btn,e) {
	var s = this.getStore();
        if (btn.text == 'ohne Auto-Newsletter') {
            s.setBaseParam('auto',1);
            btn.setText('Alle');
        } else {
            s.setBaseParam('auto',0);
            btn.setText('ohne Auto-Newsletter');
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,_renderGrouping: function(value, p, rec) {
        return rec.data.autotitle + ' (' + rec.data.auto + ')';
    }
    ,_renderNewsletter: function(value, p, rec) {
        return '<a href="?a=30&id='+ rec.data.docid +'">'+ value +'</a>';
    }
    ,_renderSender: function(value, p, rec) {
        if(!value && !rec.data.sender_email) {
            return '<span class="campaigner-default">' + _('campaigner.usedefault') +'<span>';
        }
        return value + ' &lt;' + rec.data.sender_email + '&gt;';
    }
    ,_renderState: function(value, p, rec) {
        if(value == 1) {
            return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
        }
        return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
        }
    ,_renderDate: function(value, p, rec) {
        if(value == _('unpublished')) {
            return '<span class="no">' + value + '</span>';
        }
        return value;
    }
    ,_renderGroups: function(value, p, rec) {
        var out = '';
        var id  = Ext.id();
        var tip = '';
        if(value) {
            for(var i = 0; i < value.length; i++) {
                if(value[i][2]) {
                    out += '<div class="group" title="'+ value[i][1]  +'" id="campaigner-group-' + id + '-'+ value[i][0] +'" style=" background: '+ value[i][2] +'"></div>';
                    tip += value[i][1] + ' ';
                }
            }
            p.attr = 'ext:qtip="'+ tip +'" ext:qtitle="'+ _('campaigner.groups') +'"';
        }
        return out;
    },_renderCount: function(value, p, rec) {
	var extend = '';
	if(value > 0) {
		extend += ' (' + Math.round(value*100 / rec.data.total) + '%)';
	}
	return value + extend;
    }
    ,approveNewsletter: function() {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/approve'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,unapproveNewsletter: function() {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/unapprove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.refresh();
                },scope:this}
            }
        });
    }
    ,kickNewsletter: function(e) {
        MODx.msg.confirm({
            title: 'Ausl&ouml;sen des Newsletters'
            ,text: 'Wollen Sie diesen Newsletter wirklich ausl&ouml;sen?'
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/kick'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,addNewsletter: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-newsletter'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.show(e);
        return;
    }
    ,editNewsletter: function() {
        window.location.href = '?a=30&id='+ this.menu.record.docid;
        return;
    }
    ,editProperties: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-newsletter-properties'
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.setValues(this.menu.record);
        w.show(e);
        return;
    }
    ,assignGroups: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-newsletter-groups'
            ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        w.setValues(this.menu.record);
        w.show(e);
        return;
    }
    ,testNewsletter: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-newsletter-test'
            ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        w.setValues(this.menu.record);
        w.show(e);
        return;
    }
    ,previewNewsletter: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-newsletter-preview'
            });
        w.setValues(this.menu.record);
        w.show(e);
        return;
    }
    ,removeNewsletter: function(e) {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}

        MODx.msg.confirm({
            title: _('campaigner.newsletter.remove.title')
            ,text: _('campaigner.newsletter.remove.confirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/remove'
                ,marked: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
            // ,listeners: {
            //     'success': {fn: function() {
            //         MODx.msg.confirm({
            //             title: _('campaigner.newsletter.remove.title')
            //             ,text: _('campaigner.newsletter.remove.confirm')
            //             ,url: Campaigner.config.connector_url
            //             ,params: {
            //                 action: 'mgr/newsletter/remove'
            //                 ,id: this.menu.record.docid
            //             }
            //             ,listeners: {
            //                 'success': {fn:this.refresh,scope:this}
            //             }
            //         });
            //     },scope:this}
            // }
        });
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();

            if(!this.menu.record.sent_date) {
                if(this.menu.record.state == 1) {
                    m.push({
                        text: _('campaigner.newsletter.unapprove')
                        ,handler: this.unapproveNewsletter
                    });
                } else {
                    m.push({
                        text: _('campaigner.newsletter.approve')
                        ,handler: this.approveNewsletter
                    });
                }
                m.push('-');
                m.push({
                    text: _('campaigner.newsletter.editproperties')
                    ,handler: this.editProperties
                });
                m.push({
                    text: _('campaigner.newsletter.assigngroups')
                    ,handler: this.assignGroups
                });
                m.push('-');
            }
            m.push({
                text: _('campaigner.newsletter.edit')
                ,handler: this.editNewsletter
            });
            m.push({
                text: _('campaigner.newsletter.remove')
                ,handler: this.removeNewsletter
            });
            // m.push({
            //     text: _('campaigner.newsletter.sendagain')
            //     ,handler: this.resendNewsletter
            // });
            m.push('-');
            m.push({
                    text: _('campaigner.newsletter.preview')
                    ,handler: this.previewNewsletter
                });
                m.push({
                    text: '<strong>' + _('campaigner.newsletter.sendtest') + '</strong>'
                    ,handler: this.testNewsletter
                });
            }
            m.push('-');
            m.push({
                text: _('campaigner.newsletter.kicknow')
                ,handler: this.kickNewsletter
            });
            if (m.length > 0) {
                this.addContextMenuItem(m);
            }
    }
});
Ext.reg('campaigner-grid-newsletter',Campaigner.grid.Newsletter);

Campaigner.window.Newsletter = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/newsletter/save'
        ,fields: [{
            tag: 'div'
            ,html: _('campaigner.newsletter.create.info')
            ,cls: 'campaigner-window-info'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.subject')
            ,name: 'subject'
            ,id: 'campaigner-'+this.ident+'-subject'
        }]
    });
    Campaigner.window.Newsletter.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.Newsletter,MODx.Window);
Ext.reg('campaigner-window-newsletter',Campaigner.window.Newsletter);

Campaigner.window.NewsletterProperties = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.properties')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/newsletter/properties'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident+'-id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.newsletter.sender')
            ,name: 'sender'
            ,id: 'campaigner-'+this.ident+'-sender'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.newsletter.senderemail')
            ,name: 'sender_email'
            ,id: 'campaigner-'+this.ident+'-sender-email'
        },{
            xtype: 'combo'
            ,fieldLabel: _('campaigner.newsletter.priority')
            ,name: 'campaigner'
            ,id: 'campaigner-'+this.ident+'-priority'
            ,width: 200
            ,emptyText: '5'
            ,fields: [
                'id',
                'display'
            ]
            ,store: [
                [1, 'Sehr wichtig'],
                [2, 'Schon wichtig'],
                [3, 'Wichtig'],
                [4, 'Geht so'],
                [5, 'Unwichtig']
            ]
            ,valueField: 'id'
            ,displayField: 'display'
            ,triggerAction: 'all'
            ,forceSelection: true
            ,lastQuery: ''
            ,triggerAction: 'all'
            ,hiddenName: 'priority'
        },{
            xtype: 'combo'
            ,name: 'state'
            ,id: 'campaigner-'+this.ident+'-state'
            ,store: [
                [1, _('campaigner.newsletter.approved')],
                [0, _('campaigner.newsletter.unapproved')]
            ]
            ,editable: false
            ,triggerAction: 'all'
            ,lastQuery: ''
            ,hiddenName: 'state'
            ,submitValue: false
	}]
    });
    Campaigner.window.NewsletterProperties.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.NewsletterProperties,MODx.Window);
Ext.reg('campaigner-window-newsletter-properties',Campaigner.window.NewsletterProperties);


Campaigner.window.NewsletterGroups = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.groups')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/newsletter/groups'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident+'-id'
        }]
    });
    Campaigner.window.NewsletterGroups.superclass.constructor.call(this,config);
    
    this.addListener('show', function(cmp) {
		MODx.Ajax.request({
		    url: Campaigner.config.connector_url
		    ,params: {
			action: 'mgr/group/getgrouplist'
		    }
		    ,scope: this
		    ,listeners: {
			'success': {fn: function(response) {
			    var groups = Ext.decode(response.responseText);
			    var checked = false;
			    groups = response.object;
			    
			    if(groups.length > 0) {
				Ext.each(groups, function(item, key) {
				    checked = false;
				    if(cmp.record.groups) {
					Ext.each(cmp.record.groups, function(i, k) {
					    if(item.id == i[0]) checked = true;
					});
				    }
				    this.items.items[0].add({
					xtype: 'checkbox'
					,name: 'groups[]'
					,fieldLabel: item.name
					,inputValue: item.id
					,checked: checked
					,labelSeparator: ''
					,hideLabel: true
					,boxLabel: '<span style="color: ' + item.color + ';">' + item.name + '</span>'
				    });
				}, this);
			    }
			   this.doLayout(false, true);
			}, scope: this }
		    }
		});
		
            }, this);
};
Ext.extend(Campaigner.window.NewsletterGroups,MODx.Window);
Ext.reg('campaigner-window-newsletter-groups',Campaigner.window.NewsletterGroups);

Campaigner.window.NewsletterTest = function(config) {
    config = config || {};
    config.saveBtnText = _('send');
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.sendtest')
        ,id: this.ident
        ,height: 400
        ,width: 350
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/newsletter/sendtest'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: this.ident+'-id'
        }
        ,{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.newsletter.sendtest.personalize')
            ,name: 'personalize'
            ,id: this.ident+'-personalize'
    	    ,inputValue: 1
    	    ,labelSeparator: ''
    	    ,hideLabel: true
    	    ,boxLabel: _('campaigner.newsletter.sendtest.personalize')
        }
        ,{
            xtype: 'textfield'
            ,anchor: '100%'
            ,fieldLabel: _('campaigner.newsletter.sendtest.email')
            ,name: 'email'
            ,value: MODx['config']['campaigner.test_mail']
            ,id: this.ident+'-email'
        }
        ,{
            xtype: 'textarea'
            ,grow: true
            ,anchor: '100%'
            ,fieldLabel: _('campaigner.newsletter.sendtest.instructions')
            ,name: 'instructions'
            ,value: MODx['config']['campaigner.test_instructions']
            ,id: this.ident+'-instructions'
        }
        ,{
    	    tag: 'div'
    	    ,html: '<span>' + _('campaigner.or') + '</span>'
    	    ,cls: 'campaigner-spacer'
        }
        ,{
            tag: 'div'
            ,html: '<span>' + _('campaigner.newsletter.sendtest.selectgroup') + '</span>'
	   }]
    });
    Campaigner.window.NewsletterTest.superclass.constructor.call(this,config);
    
        this.addListener('show', function(cmp) {
		MODx.Ajax.request({
		    url: Campaigner.config.connector_url
		    ,params: {
			action: 'mgr/group/getgrouplist'
		    }
		    ,scope: this
		    ,listeners: {
			'success': {fn: function(response) {
			    var groups = Ext.decode(response.responseText);
			    groups = response.object;
			    
			    if(groups.length > 0) {
				Ext.each(groups, function(item, key) {
				    this.items.items[0].add({
					xtype: 'checkbox'
					,name: 'groups[]'
					,fieldLabel: item.name
					,inputValue: item.id
					,checked: false
					,labelSeparator: ''
					,hideLabel: true
					,boxLabel: '<span style="color: ' + item.color + ';">' + item.name + '</span>'
				    });
				}, this);
			    }
			   this.doLayout(false, true);
			}, scope: this }
		    }
		});
		
            }, this);
};
Ext.extend(Campaigner.window.NewsletterTest,MODx.Window);
Ext.reg('campaigner-window-newsletter-test',Campaigner.window.NewsletterTest);

Campaigner.window.NewsletterPreview = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    this.message = '';
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.preview')
        ,id: this.ident
        ,height: 600
        ,width: 750
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/newsletter/sendtest'
        ,saveBtnText: _('campaigner.newsletter.sendtest')
        ,cancelBtnText: _('campaigner.close')
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: this.ident+'-id'
        },
        {
            xtype: 'container'
            ,layout: {
                type: 'hbox',
            }
            ,items: [
            {
                xtype: 'radiogroup'
                ,flex: 0.5
                ,fieldLabel: _('campaigner.newsletter.preview.persona')
                ,name: 'persona'
                ,id: this.ident+'-persona'
                ,columns: 1
                ,items: [
                    {boxLabel: _('campaigner.newsletter.preview.nopersona'), name: 'persona', inputValue: '', checked: true},
                    {boxLabel: _('campaigner.newsletter.preview.personalize'), name: 'persona', inputValue: 1},
                ]
                ,listeners: {
                    'change': {fn: function() {
                        this.fireEvent('show');
                        Ext.get(this.ident+'-email').toggleClass('campaigner-hidden');
                    }, scope: this }
                }
            },{
                xtype: 'textfield'
                ,flex: 0.5
                ,fieldLabel: ''
                ,name: 'email'
                ,value: MODx['config']['campaigner.test_mail']
                ,id: this.ident+'-email'
                ,cls: 'campaigner-hidden'
                ,listeners: {
                    'change': { fn: function() {
                        this.fireEvent('show');
                    }, scope: this }
                }
            },
            {
                xtype: 'checkbox'
                ,flex: 1
                ,id: this.ident+'-tags'
                ,labelSeparator: ''
                ,hideLabel: true
                ,boxLabel: _('campaigner.newsletter.preview.process_tags')
                ,fieldLabel: _('campaigner.newsletter.preview.process_tags')
                ,listeners: {
                    'check': {fn: function(btn) {
                        this.fireEvent('show');
                    }, scope: this}
                }
            },
            ]
        }
        ,{
            xtype: 'button'
            ,flex: 1
            ,id: this.ident+'-text'
            ,text: _('campaigner.newsletter.preview.showtext')
            ,listeners: {
                'click': {fn: function(btn) {
                    if(btn.text == _('campaigner.newsletter.preview.showhtml')) {
                        btn.setText(_('campaigner.newsletter.preview.showtext'));
                        Ext.get(this.ident+'-preview-box').update(this.message.message);
                    } else {
                        btn.setText(_('campaigner.newsletter.preview.showhtml'));
                        Ext.get(this.ident+'-preview-box').update(this.message.text);
                    }
                }, scope: this}
            }
        }
        ,{
            tag: 'div'
            ,border: true
            ,id: this.ident+'-preview'
            ,style: 'height:500px;overflow:auto;padding:10px 0;border:1px solid #ccc'
            ,html: '<div id="'+this.ident+'-preview-box"></div>'
        }]
        // ,buttons: [{
        //     text: _('close')
        //     ,scope: this
        //     ,handler: function() { this.hide(); }
        // },{
        //     text: _()
        // }]
    });
    Campaigner.window.NewsletterPreview.superclass.constructor.call(this,config);
    
    this.addListener('show', function(cmp) {
        var email;
        if(this.findById(this.ident+'-persona').getValue().inputValue == 1) {
            email = this.findById(this.ident+'-email').getValue();
        }
        
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/newsletter/preview'
                ,id: this.findById(this.ident+'-id').getValue()
                ,email: email
                ,tags: this.findById(this.ident+'-tags').getValue()
            }
            ,scope: this
            ,listeners: {
                'success': {fn: function(response) {
                    var message = Ext.decode(response.responseText);
                    this.message = response.object;
                    if(this.findById(this.ident +'-text').text == _('campaigner.newsletter.preview.showtext')) {
                        message = this.message.message;
                    } else {
                        message = this.message.text;
                    }
                    Ext.get(this.ident+'-preview-box').update(message);
                }, scope: this }
            }
        });
    }, this);
};
Ext.extend(Campaigner.window.NewsletterPreview,MODx.Window);
Ext.reg('campaigner-window-newsletter-preview',Campaigner.window.NewsletterPreview);


Ext.override(Ext.ToolTip, {
    onTargetOver : function(e){
        if(this.disabled || e.within(this.target.dom, true)){
            return;
        }
        var t = e.getTarget(this.delegate);
        if (t) {
            this.triggerElement = t;
            this.clearTimer('hide');
            this.targetXY = e.getXY();
            this.delayShow();
        }
    },
    onMouseMove : function(e){
        var t = e.getTarget(this.delegate);
        if (t) {
            this.targetXY = e.getXY();
            if (t === this.triggerElement) {
                if(!this.hidden && this.trackMouse){
                    this.setPagePosition(this.getTargetXY());
                }
            } else {
                this.hide();
                this.lastActive = new Date(0);
                this.onTargetOver(e);
            }
        } else if (!this.closable && this.isVisible()) {
            this.hide();
        }
    },
    hide: function(){
        this.clearTimer('dismiss');
        this.lastActive = new Date();
        delete this.triggerElement;
        Ext.ToolTip.superclass.hide.call(this);
    }
});

