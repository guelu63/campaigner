Campaigner.grid.Autonewsletter = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/autonewsletter/getList' }
        ,fields: ['id', 'docid', 'groups', 'state', 'start', 'last', 'frequency', 'time', 'sender', 'sender_email', 'subject', 'date', 'description']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: _('campaigner.newsletter.subject')
            ,dataIndex: 'subject'
            ,sortable: true
            ,width: 40
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.newsletter.sender')
            ,dataIndex: 'sender'
            ,sortable: true
            ,width: 20
            ,renderer: this._renderSender
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
            ,width: 15
            ,renderer: this._renderGroups
        },{
            header: _('campaigner.autonewsletter.start')
            ,dataIndex: 'start'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.autonewsletter.last')
            ,dataIndex: 'last'
            ,sortable: true
            ,width: 15
        },{
            header: _('campaigner.autonewsletter.frequency')
            ,dataIndex: 'frequency'
            ,sortable: true
            ,width: 15
            ,renderer: this._renderFrequency
        },{
            header: _('campaigner.autonewsletter.time')
            ,dataIndex: 'time'
            ,sortable: true
            ,width: 10
        }],
        /* Top toolbar */
        tbar : ['->', {
            xtype: 'combo'
            ,name: 'state'
            ,id: 'campaigner-filter-auto-state'
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
        }]
    });
    Campaigner.grid.Autonewsletter.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Autonewsletter,MODx.grid.Grid,{
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
    ,_renderNewsletter: function(value, p, rec) {
        return '<a href="?a=30&id='+ rec.data.docid +'">'+ value +'</a><br/><strong><small>' + rec.data.description + '</small></strong>';
    }
    ,_renderState: function(value, p, rec) {
        if(value == 1)
           return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
       return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
    }
    ,_renderGroups: function(value, p, rec) {
        var out = '';
        var tip = '';

        if(value) {
            for(var i = 0; i < value.length; i++) {
                if(value[i][2]) {
                    out += '<div class="group" style=" background: '+ value[i][2] +'"></div>';
                    tip += value[i][1] + ' ';
                }
                p.attr = 'ext:qtip="'+ tip +'" ext:qtitle="'+ _('campaigner.groups') +'"';
            }
        }
        return out;
    }
    ,_renderSender: function(value, p, rec) {
        if(!value && !rec.data.sender_email) {
           return '<span class="campaigner-default">' + _('campaigner.usedefault') +'<span>';
       }
       return value + ' &lt;' + rec.data.sender_email + '&gt;';
    }
    ,_renderFrequency: function(value, p, rec) {
        if(value % 604800 === 0) {
            rec.data.interval  = 7;
            rec.data.frequency = value/604800;
            return  (value/604800) + ' ' + _('campaigner.weeks');
        }
        if(value % 86400 === 0) {
            rec.data.interval  = 1;
            rec.data.frequency = value/86400;
            return  (value/86400) + ' ' + _('campaigner.days');
        }
        return value;
    }
    ,approveNewsletter: function() {
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/autonewsletter/approve'
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
                action: 'mgr/autonewsletter/unapprove'
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
         ,text: 'Wollen Sie diesen Newsletter wirklich ausl&ouml;sen?<br/>Zuletzt ausgel&ouml;st: <strong>' + this.menu.record.last + ' um ' + this.menu.record.time + '</strong>'
         ,url: Campaigner.config.connector_url
         ,params: {
            action: 'mgr/autonewsletter/kick'
            ,id: this.menu.record.id
        }
        ,listeners: {
            'success': {fn:this.refresh,scope:this}
        }
    });
    }
    ,editNewsletter: function() {
        window.location.href = '?a=30&id='+ this.menu.record.docid;
        return;
    }
    ,removeNewsletter: function(e) {
        MODx.msg.confirm({
            title: _('campaigner.autonewsletter.remove.title')
            ,text: _('campaigner.autonewsletter.remove.confirm')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/autonewsletter/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,editProperties: function(e) {
        var w = MODx.load({
           xtype: 'campaigner-window-autonewsletter-properties'
           ,record: this.menu.record
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
            xtype: 'campaigner-window-autonewsletter-groups'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
        w.show(e);
        return;
    }
    ,previewNewsletter: function(e) {
        var w = MODx.load({
            xtype: 'campaigner-window-autonewsletter-preview'
            });
        w.setValues(this.menu.record);
        w.show(e);
        return;
    }
    ,testNewsletter: function(e) {
        var w = MODx.load({
           xtype: 'campaigner-window-autonewsletter-test'
           ,record: this.menu.record
           ,listeners: {
            'success': {fn:this.refresh,scope:this}
        }
    });
        w.show(e);
        return;
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();

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
                text: _('campaigner.newsletter.properties')
                ,handler: this.editProperties
            });
            m.push({
                text: _('campaigner.newsletter.assigngroups')
                ,handler: this.assignGroups
            });
            m.push('-');
            m.push({
                text: _('campaigner.newsletter.edit')
                ,handler: this.editNewsletter
            });
            m.push('-');
            m.push({
                text: _('campaigner.newsletter.kicknow')
                ,handler: this.kickNewsletter
            });
            m.push('-');
            m.push({
                text: _('campaigner.newsletter.preview')
                ,handler: this.previewNewsletter
            });
            m.push({
                text: _('campaigner.newsletter.sendtest')
                ,handler: this.testNewsletter
            });
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
});
Ext.reg('campaigner-grid-autonewsletter',Campaigner.grid.Autonewsletter);


Campaigner.window.AutonewsletterProperties = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.properties')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,defaults: {flex: 1, layout: 'form', border: false}
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/autonewsletter/properties'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: 'campaigner-'+this.ident+'-id'
        },{
            xtype: 'combo'
            ,fieldLabel: _('campaigner.newsletter.state')
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
        },{
            xtype: 'container'
            ,layout: {
                type: 'hbox',
                align: 'stretch'
            }
            ,fieldLabel: _('campaigner.newsletter.date_time')
            ,defaults: {flex: 1, layout: 'form', border: false, margins: '0 5 0 0'}
            ,style: 'border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 15px'
            ,items: [
            {
                xtype: 'datefield'
                ,fieldLabel: _('campaigner.autonewsletter.start')
                ,name: 'start'
                ,format: 'd.m.Y'
                ,id: 'campaigner-'+this.ident+'-start'
            },{
                xtype: 'timefield'
                ,fieldLabel: _('campaigner.autonewsletter.time')
                ,name: 'time'
                ,id: 'campaigner-'+this.ident+'-time'
                ,format: 'H:i:s'
            }
            ]
        },{
            xtype: 'container'
            ,layout: {
                type: 'hbox',
                align: 'stretch'
            }
            ,fieldLabel: _('campaigner.newsletter.repeat')
            ,defaults: {flex: 1, layout: 'form', border: false, margins: '0 5 0 0'}
            ,style: 'border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 15px'
            ,items: [
            {
                xtype: 'numberfield'
                ,fieldLabel: _('campaigner.autonewsletter.frequency')
                ,name: 'frequency'
                ,id: 'campaigner-'+this.ident+'-frequency'
            },{
                xtype: 'combo'
                ,name: 'interval'
                ,id: 'campaigner-'+this.ident+'-interval'
                ,store: [
                [1, _('campaigner.days')],
                [7, _('campaigner.weeks')],
                [30, _('campaigner.months')]
                ]
                ,editable: false
                ,triggerAction: 'all'
                ,lastQuery: ''
                ,hiddenName: 'interval'
                ,submitValue: false
            },{
                xtype: 'combo'
                ,name: 'interval'
                ,emptyText: _('campaigner.weekdays')
                ,id: 'campaigner-'+this.ident+'-weekday'
                ,store: [
                    [1, _('campaigner.day.1')],
                    [2, _('campaigner.day.2')],
                    [3, _('campaigner.day.3')],
                    [4, _('campaigner.day.4')],
                    [5, _('campaigner.day.5')],
                    [6, _('campaigner.day.6')],
                    [7, _('campaigner.day.7')]
                ]
                ,editable: false
                ,triggerAction: 'all'
                ,lastQuery: ''
                ,hiddenName: 'interval'
                ,submitValue: false
            }
            ]
        },{
            xtype: 'container'
            ,layout: {
                type: 'hbox',
                align: 'stretch'
            }
            ,fieldLabel: _('campaigner.newsletter.sender_email')
            ,defaults: {flex: 1, layout: 'form', border: false, margins: '0 5 0 0'}
            ,style: 'border-bottom: 1px solid #ccc; margin-bottom: 15px; padding-bottom: 15px'
            ,items: [
            {
                xtype: 'textfield'
                ,fieldLabel: _('campaigner.newsletter.sender')
                ,name: 'sender'
                ,id: 'campaigner-'+this.ident+'-sender'
                ,flex: 1
            },{
                xtype: 'textfield'
                ,fieldLabel: _('campaigner.newsletter.senderemail')
                ,name: 'sender_email'
                ,id: 'campaigner-'+this.ident+'-sender-email'
                ,flex: 1
            }
            ]
        },{
            xtype: 'textarea'
            ,width: 300
            ,fieldLabel: _('campaigner.autonewsletter.description')
            ,name: 'description'
            ,placeholder: 'A description of the autonewsletter'
            ,id: 'campaigner-'+this.ident+'-description'
        }]
    });
Campaigner.window.AutonewsletterProperties.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.AutonewsletterProperties,MODx.Window);
Ext.reg('campaigner-window-autonewsletter-properties',Campaigner.window.AutonewsletterProperties);


Ext.onReady(function() {

    var checkboxArray = [];
    var groupsStore = new Ext.data.JsonStore({
        url: Campaigner.config.connector_url
        ,root: "object"
        ,baseParams: {
            action: "mgr/group/getSubscriberList"
        }
        ,fields: ["id", "name", "color"]
        ,autoLoad: false
        ,listeners: {
            load: function(t, records, options) {
                // this.ident = config.ident || 'campaigner-'+Ext.id();
                console.log(this);
                for(var i = 0; i < records.length; i++) {
                    console.log(records[i].data.name);
                    console.log(i[0]);
                    checkboxArray.push({name: "groups[]", inputValue: records[i].data.id, boxLabel: records[i].data.name});
                }
            }
        }
    });
    groupsStore.load();

    Campaigner.window.AutonewsletterGroups = function(config) {
        config = config || {};

        this.ident = config.ident || 'campaigner-'+Ext.id();
        Ext.applyIf(config,{
            title: _('campaigner.newsletter.groups')
            ,id: this.ident
            ,height: 400
            ,width: 475
            ,url: Campaigner.config.connector_url
            ,action: 'mgr/autonewsletter/groups'
            ,fields: [{
                xtype: 'hidden'
                ,name: 'id'
                ,id: 'campaigner-'+this.ident+'-id'
            },{
                xtype: 'checkboxgroup'
                ,fieldLabel: _('campaigner.newsletter.groups_add')
                ,name: 'groups'
                ,items: checkboxArray
            }]
        });
        Campaigner.window.AutonewsletterGroups.superclass.constructor.call(this,config);

        // this.addListener('show', function(cmp) {
        //     MODx.Ajax.request({
        //         url: Campaigner.config.connector_url
        //         ,params: {
        //             action: 'mgr/group/getSubscriberList'
        //         }
        //         ,scope: this
        //         ,listeners: {
        //             'success': {fn: function(response) {
        //                 var groups = Ext.decode(response.responseText);
        //                 var checked = false;
        //                 groups = response.object;
        //                 var myArray = [];
        //                 if(groups.length > 0) {
        //                     Ext.each(groups, function(item, key) {
        //                         checked = false;
        //                         if(cmp.record.groups) {
        //                             Ext.each(cmp.record.groups, function(i, k) {
        //                                 if(item.id == i[0]) checked = true;
        //                             });
        //                         }
        //                         myArray.push({
        //                             xtype: 'checkbox'
        //                             ,name: 'groups[]'
        //                             ,fieldLabel: item.name
        //                             ,inputValue: item.id
        //                             ,checked: checked
        //                             ,labelSeparator: ''
        //                             ,hideLabel: true
        //                             ,boxLabel: '<span style="color: ' + item.color + ';">' + item.name + '</span>'
        //                         });
        //                     }, this);
        //                 }
        //                 this.doLayout(false, true);
        //             }, scope: this }
        //         }
        //     });
        // }, this);
        // 
    };

    Ext.extend(Campaigner.window.AutonewsletterGroups,MODx.Window);
    Ext.reg('campaigner-window-autonewsletter-groups',Campaigner.window.AutonewsletterGroups);
});

Campaigner.window.AutonewsletterTest = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.sendtest')
        ,id: this.ident
        ,height: 400
        ,width: 475
        ,saveBtnText: _('campaigner.newsletter.sendtest')
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/autonewsletter/sendtest'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: this.ident+'-id'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.newsletter.sendtest.personalize')
            ,name: 'personalize'
            ,id: this.ident+'-personalize'
            ,inputValue: 1
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.newsletter.sendtest.personalize')
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('campaigner.newsletter.sendtest.attachments_add')
            ,name: 'add_attachments'
            ,id: this.ident+'-add_attachments'
            ,inputValue: 1
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.newsletter.sendtest.add_attachments')
        },{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.newsletter.sendtest.email')
            ,name: 'email'
            ,value: MODx['config']['campaigner.test_mail']
            ,id: this.ident+'-email'
        }, {
           tag: 'div'
           ,html: '<span>' + _('campaigner.or') + '</span>'
           ,cls: 'campaigner-spacer'
       }, {
           tag: 'div'
           ,html: '<span>' + _('campaigner.newsletter.sendtest.selectgroup') + '</span>'
       }, {
           tag: 'div'
           ,cls: 'campaigner-loader'
       }]
    });
    Campaigner.window.AutonewsletterTest.superclass.constructor.call(this,config);

    // listener to dynamic adding of groups selection
    this.addListener('show', function(cmp) {
       MODx.Ajax.request({
          url: Campaigner.config.connector_url
          ,params: {
             action: 'mgr/group/getSubscriberList'
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
Ext.extend(Campaigner.window.AutonewsletterTest,MODx.Window);
Ext.reg('campaigner-window-autonewsletter-test',Campaigner.window.AutonewsletterTest);


Campaigner.window.AutonewsletterPreview = function(config) {
    config = config || {};
    this.ident = config.ident || 'campaigner-'+Ext.id();
    this.message = '';
    Ext.applyIf(config,{
        title: _('campaigner.newsletter.preview')
        ,id: this.ident
        ,height: 600
        ,width: 750
        ,url: Campaigner.config.connector_url
        ,action: 'mgr/autonewsletter/sendtest'
        ,saveBtnText: _('campaigner.newsletter.sendtest')
        ,cancelBtnText: _('campaigner.close')
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
            ,id: this.ident+'-id'
        },{
            xtype: 'radiogroup'
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
        }, {
            xtype: 'button'
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
        }, {
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
    Campaigner.window.AutonewsletterPreview.superclass.constructor.call(this,config);
    
    this.addListener('show', function(cmp) {
        var email;
        if(this.findById(this.ident+'-persona').getValue().inputValue == 1) {
            email = this.findById(this.ident+'-email').getValue();
        }
        MODx.Ajax.request({
            url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/autonewsletter/preview'
                ,id: this.findById(this.ident+'-id').getValue()
                ,email: email
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
Ext.extend(Campaigner.window.AutonewsletterPreview,MODx.Window);
Ext.reg('campaigner-window-autonewsletter-preview',Campaigner.window.AutonewsletterPreview);