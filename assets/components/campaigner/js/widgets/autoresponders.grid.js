Campaigner.grid.AutoResponders = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/autoresponders/getlist' }
        ,fields: ['id', 'name', 'event', 'field', 'time', 'delay_value', 'delay_unit', 'weekday']
        ,paging: true
        ,remoteSort: true
        // ,primaryKey: 'id'
        // ,save_action: 'mgr/socialsharing/updateFromGrid'
        // ,autosave: true
        ,columns: [{hidden: true, dataIndex: 'weekday'},{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 3
        },{
            header: _('campaigner.autoresponders.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 7
        },{
            header: _('campaigner.autoresponders.active')
            ,dataIndex: 'active'
            ,sortable: true
            ,width: 5
            ,renderer: function(value, p, rec) {
                if(value == 1) {
                    return '<img src="'+ Campaigner.config.base_url +'images/mgr/yes.png" alt="' + _('campaigner.newsletter.approved') + '" />';
                }
                return '<img src="'+ Campaigner.config.base_url +'images/mgr/no.png" alt="' + _('campaigner.newsletter.unapproved') + '" />';
            }
        },{
            header: _('campaigner.autoresponders.event')
            ,dataIndex: 'event'
            ,sortable: true
            ,width: 7
        },{
            header: _('campaigner.autoresponders.field')
            ,dataIndex: 'field'
            ,sortable: true
            ,width: 7
        },{
           header: _('campaigner.autoresponders.delay_value')
            ,dataIndex: 'delay_value'
            ,sortable: true
            ,width: 7 
        },{
           header: _('campaigner.autoresponders.delay_unit')
            ,dataIndex: 'delay_unit'
            ,sortable: true
            ,width: 7 
        },{
            header: _('campaigner.autoresponders.time')
            ,dataIndex: 'time'
            ,sortable: true
            ,width: 7
        }]
         /* Top toolbar */  
        ,tbar : [{
    	    xtype: 'button'
                ,text: _('campaigner.autoresponders.add')
                ,handler: { xtype: 'campaigner-window-autoresponders-add' ,blankValues: true }
                ,disabled: !MODx.perm.field_create
        }]
        	 // }, '->', {
          //           xtype: 'combo'
          //           ,name: 'public'
          //           ,id: 'campaigner-filter-public'
        	 //    ,store: [
        		// ['-', _('campaigner.all')],
          //               [1, _('campaigner.socialsharing.public')],
          //               [0, _('campaigner.socialsharing.private')]
          //           ]
          //           ,editable: false
          //           ,triggerAction: 'all'
          //           ,lastQuery: ''
          //           ,hiddenName: 'public'
          //           ,submitValue: false
        	 //    ,emptyText: _('campaigner.socialsharing.filter.public')
          //           ,listeners: {
          //               'change': {fn: this.filterPublic, scope: this}
          //               ,'render': {fn: function(cmp) {
          //                   new Ext.KeyMap(cmp.getEl(), {
          //                       key: Ext.EventObject.ENTER
          //                       ,fn: function() {
          //                           this.fireEvent('change',this.getValue());
          //                           this.blur();
          //                           return true;}
          //                       ,scope: cmp
          //                   });
          //               },scope:this}
          //           }
    });
    Campaigner.grid.AutoResponders.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.AutoResponders,MODx.grid.Grid, {
    getMenu: function() {
        return [{
            text: _('campaigner.autoresponders.update')
            ,handler: this.updateAutoResponders
            ,disabled: !MODx.perm.field_edit
        },'-',{
            text: _('campaigner.autoresponders.remove')
            ,handler: this.removeAutoResponders
            ,disabled: !MODx.perm.field_remove
        }];
    }
    ,updateAutoResponders: function(btn,e) {
        if (!this.updateAutoRespondersWindow) {
            this.updateAutoRespondersWindow = MODx.load({
                xtype: 'campaigner-window-autoresponders-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        this.updateAutoRespondersWindow.setValues(this.menu.record);
        this.updateAutoRespondersWindow.show(e.target);
    }
    ,removeAutoResponders: function() {
        MODx.msg.confirm({
            title: _('campaigner.autoresponders.remove')
            ,text: _('campaigner.autoresponders.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/autoresponders/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('campaigner-grid-autoresponders',Campaigner.grid.AutoResponders);

Campaigner.window.CreateAutoResponders = function(config) {
    Ext.QuickTips.init();
    config = config || {};
    Ext.applyIf(config,{
        title: _('campaigner.autoresponders.add')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/autoresponders/create'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.autoresponders.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'xcheckbox'
            ,fieldLabel: _('campaigner.autoresponders.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.autoresponders.active')
            ,name: 'active'
        },{
            xtype: 'modx-combo'
            ,hiddenName: 'options[event]'
            ,fieldLabel: _('campaigner.autoresponders.event')
            ,store: new Ext.data.ArrayStore({
                id: 0
                ,fields: ['value','display']
                ,data: [
                    ['subscription','Anmeldung']
                    ,['','Newsletter-Event']
                    ,['opened','Geöffnet']
                    ,['anylink','Ein Link geklickt']
                    ,['speclink','Spezieller Link geklick']
                    ,['','Feld-Event']
                    ,['annual','Jährlich wiederkehrend']
                    ,['birthday','Geburtstag']
                    ,['period','Registriert seit']
                ]
            })
            ,mode: 'local'
            ,displayField: 'display'
            ,valueField: 'value'
            ,listeners: {
                'change': {fn: function(t, n, o) {
                    var fields_custom;
                    var fieldset = Ext.getCmp('autoresponder-values');
                    fieldset.removeAll();

                    if(n == 'opened') {
                        fields_custom = [{
                            xtype: 'campaigner-combo-newsletter'
                        }]
                    }

                    if(n == 'anylink') {
                        fields_custom = [{
                            xtype: 'campaigner-combo-newsletter'
                        }]
                    }

                    if(n == 'speclink') {
                        fields_custom = [{
                            xtype: 'campaigner-combo-newsletter'
                        },{
                            xtype: ''
                        }]
                    }

                    if(n == 'period') {
                        fields_custom = [{
                            xtype: 'textfield'
                            ,name: 'options[period_value]'
                            ,fieldLabel: _('campaigner.autoresponders.period_value')
                            ,labelSeparator: ''
                        },{
                            xtype: 'modx-combo'
                            ,fieldLabel: _('campaigner.autoresponders.period_unit')
                            ,hiddenName: 'options[period_unit]'
                            ,store: new Ext.data.ArrayStore({
                                id: 0
                                ,fields: ['value','display']
                                ,data: [
                                    [3600,'Stunde(n)']
                                    ,[86400,'Tag(e)']
                                    ,[604800,'Woche(n)']
                                    ,[2628000,'Monat(e)']
                                    ,[31556926,'Jahr(e)']
                                ]
                            })
                            ,mode: 'local'
                            ,displayField: 'display'
                            ,valueField: 'value'
                        }];
                    }

                    if(n == 'birthday') {
                        fields_custom = [{
                            xtype: 'campaigner-combo-arfields'
                            ,type: 'birthday'
                        }]
                    }
                    if(n == 'annual') {
                        fields_custom = [
                        {
                            xtype: 'textfield'
                            ,name: 'fields[0]'
                            ,fieldLabel: 'Test'
                            ,labelSeparator: ''
                        },{
                            xtype: 'textfield'
                            ,name: 'fields[0]'
                            ,fieldLabel: 'Test'
                            ,labelSeparator: ''
                        }];
                    }

                    // Refresh view
                    fieldset.add(fields_custom);
                    fieldset.doLayout();
                }, scope: this }
            }
        },{
            xtype: 'fieldset'
            ,id: 'autoresponder-values'
            ,title: 'Fieldset 1'
            ,collapsible: true
            ,defaultType: 'textfield'
            ,defaults: {
                anchor: '100%'
            }
            ,layout: 'form'
        },{
            xtype: 'container'
            ,layout: 'hbox'
            ,items: [{
                xtype: 'textfield'
                ,fieldLabel: _('campaigner.autoresponders.delay_value')
                ,name: 'options[delay_value]'
                ,flex: 0.5
            },{
                xtype: 'modx-combo'
                ,fieldLabel: _('campaigner.autoresponders.delay_unit')
                ,hiddenName: 'options[delay_unit]'
                ,store: new Ext.data.ArrayStore({
                    id: 0
                    ,fields: ['value','display']
                    ,data: [
                        [3600,'Stunde(n)']
                        ,[86400,'Tag(e)']
                        ,[604800,'Woche(n)']
                        ,[2628000,'Monat(e)']
                        ,[31556926,'Jahr(e)']
                    ]
                })
                ,mode: 'local'
                ,displayField: 'display'
                ,valueField: 'value'
                ,flex: 0.5
            }]
        },{
            xtype: 'checkboxgroup',
            name: 'options[weekday]',
            fieldLabel: 'Wochentag',
            // Arrange checkboxes into two columns, distributed vertically
            columns: 7,
            vertical: true,
            items: [{
                boxLabel: 'Mo',
                name: 'options[weekday][]',
                inputValue: '1'
            },{
                boxLabel: 'Tu',
                name: 'options[weekday][]',
                inputValue: '2'
            },{
                boxLabel: 'We',
                name: 'options[weekday][]',
                inputValue: '3'
            },{
                boxLabel: 'Th',
                name: 'options[weekday][]',
                inputValue: '4'
            },{
                boxLabel: 'Fr',
                name: 'options[weekday][]',
                inputValue: '5'
            },{
                boxLabel: 'Sa',
                name: 'options[weekday][]',
                inputValue: '6'
            },{
                boxLabel: 'So',
                name: 'options[weekday][]',
                inputValue: '7'
            }]
        },{
            xtype: 'timefield'
            ,name: 'options[time]'
            ,format: 'H:i'
        }]
    });
    Campaigner.window.CreateAutoResponders.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.CreateAutoResponders,MODx.Window);
Ext.reg('campaigner-window-autoresponders-add',Campaigner.window.CreateAutoResponders);

Campaigner.window.UpdateAutoResponders = function(config) {
    config = config || {};
    console.log(config.record);
    Ext.applyIf(config,{
        title: _('campaigner.autoresponders.update')
        ,url: Campaigner.config.connectorUrl
        ,baseParams: {
            action: 'mgr/autoresponders/update'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('campaigner.autoresponders.name')
            ,name: 'name'
            ,anchor: '100%'
            ,allowBlank: false
            ,blankText: _('campaigner.field.required')
        },{
            xtype: 'xcheckbox'
            ,fieldLabel: _('campaigner.autoresponders.active')
            ,labelSeparator: ''
            ,hideLabel: true
            ,boxLabel: _('campaigner.autoresponders.active')
            ,name: 'active'
            ,value: config.record.active

        },{
            xtype: 'modx-combo'
            ,hiddenName: 'options[event]'
            ,fieldLabel: _('campaigner.autoresponders.event')
            ,anchor: '100%'
            ,store: new Ext.data.ArrayStore({
                id: 0
                ,fields: ['value','display']
                ,data: [
                    ['subscription','Anmeldung']
                    ,['','Newsletter-Event']
                    ,['opened','Geöffnet']
                    ,['anylink','Ein Link geklickt']
                    ,['speclink','Spezieller Link geklick']
                    ,['','Feld-Event']
                    ,['annual','Jährlich wiederkehrend']
                    ,['birthday','Geburtstag']
                ]
            })
            ,mode: 'local'
            ,displayField: 'display'
            ,valueField: 'value'
            ,value: config.record.event
            ,listeners: {
                'select': {fn: function(c, r, i) {
                    // var fields_custom = [
                    // {
                    //     xtype: 'textfield'
                    //     ,name: 'fields[0]'
                    //     ,fieldLabel: 'Test'
                    //     ,anchor: '100%'
                    // }];
                    // // console.log(Ext.getCmp('autoresponder-values'));
                    // // Ext.get('autoresponder-values').add(config);
                    // var form = btn.up('window').down('form'); // this is a better approach
                    console.log(form);
                    // form.add(fields_custom);
                    // this.fields[6] = config;
                }, scope: this }
            }
        },{
            xtype: 'fieldset'
            ,id: 'autoresponder-values'
            ,title: 'Fieldset 1'
            ,collapsible: true
            ,defaultType: 'textfield'
            ,defaults: {
                anchor: '100%'
            }
            ,layout: 'anchor'
            ,items: [{
                xtype: 'modx-combo'
                ,hiddenName: 'options[field]'
                ,displayField: 'display'
                ,valueField: 'value'
                ,store: new Ext.data.JsonStore({
                    url: Campaigner.config.connector_url
                    ,baseParams: {
                        action : 'mgr/autoresponders/getfields'
                        ,eventval: 'birthday'
                    }
                    ,fields: ['value','display']
                    ,root: 'results'
                })
                ,value: config.record.field
                ,mode: 'remote'
                // ,url: Campaigner.config.connectorUrl
                // ,fields: ['value', 'display']
                // ,baseParams: {
                //     action: 'mgr/autoresponders/getfields'
                //     ,event: 'birthday'
                // }
            }]
        },{
            xtype: 'panel'
            ,layout: 'hbox'
            ,items: [{
                xtype: 'textfield'
                ,fieldLabel: _('campaigner.autoresponders.delay_value')
                ,name: 'options[delay_value]'
                ,flex: 0.5
                ,value: config.record.delay_value
            },{
                xtype: 'modx-combo'
                ,fieldLabel: _('campaigner.autoresponders.delay_unit')
                ,hiddenName: 'options[delay_unit]'
                ,store: new Ext.data.ArrayStore({
                    id: 0
                    ,fields: ['value','display']
                    ,data: [
                        [3600,'Stunde(n)']
                        ,[86400,'Tag(e)']
                        ,[604800,'Woche(n)']
                        ,[2628000,'Monat(e)']
                        ,[31556926,'Jahr(e)']
                    ]
                })
                ,mode: 'local'
                ,displayField: 'display'
                ,valueField: 'value'
                ,flex: 0.5
                ,value: config.record.delay_unit
            }]
        },{
            xtype: 'checkboxgroup'
            ,hidden: true
            ,name: 'options[weekday]'
            ,fieldLabel: _('campaigner.autoresponders.weekday') + ' (' + config.record.weekday + ')'
            ,columns: 4
            ,vertical: true
            ,items: [{
                boxLabel: 'Mo (1)'
                ,name: 'options[weekday][]'
                ,inputValue: '1'
            },{
                boxLabel: 'Tu (2)'
                ,name: 'options[weekday][]'
                ,inputValue: '2'
            },{
                boxLabel: 'We (3)'
                ,name: 'options[weekday][]'
                ,inputValue: '3'
            },{
                boxLabel: 'Th (4)'
                ,name: 'options[weekday][]'
                ,inputValue: '4'
            },{
                boxLabel: 'Fr (5)'
                ,name: 'options[weekday][]'
                ,inputValue: '5'
            },{
                boxLabel: 'Sa (6)'
                ,name: 'options[weekday][]'
                ,inputValue: '6'
            },{
                boxLabel: 'So (7)'
                ,name: 'options[weekday][]'
                ,inputValue: '7'
            }]
        },{
            xtype: 'timefield'
            ,name: 'options[time]'
            ,fieldLabel: _('campaigner.autoresponders.time')
            ,format: 'H:i'
            ,value: config.record.time
        }]
    });
    Campaigner.window.UpdateAutoResponders.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.window.UpdateAutoResponders,MODx.Window);
Ext.reg('campaigner-window-autoresponders-update',Campaigner.window.UpdateAutoResponders);

Campaigner.combo.Newsletter = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        hiddenName: 'options[newsletter]'
        ,displayField: 'display'
        ,fieldLabel: _('campaigner.autoresponders.newsletter')
        ,valueField: 'value'
        ,store: new Ext.data.JsonStore({
            url: Campaigner.config.connector_url
            ,baseParams: {
                action : 'mgr/autoresponders/getnewsletter'
            }
            ,fields: ['value','display']
            ,root: 'results'
        })
        ,mode: 'remote'
    });
    Campaigner.combo.Newsletter.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.combo.Newsletter,MODx.combo.ComboBox);
Ext.reg('campaigner-combo-newsletter', Campaigner.combo.Newsletter);

Campaigner.combo.ARFields = function(config) {
    config = config || {};
    var type = config.type;
    Ext.applyIf(config,{
        hiddenName: 'options[field]'
        ,displayField: 'display'
        ,fieldLabel: _('campaigner.autoresponders.field')
        ,valueField: 'value'
        ,store: new Ext.data.JsonStore({
            url: Campaigner.config.connector_url
            ,baseParams: {
                action : 'mgr/autoresponders/getfields'
                ,type: type
            }
            ,fields: ['value','display']
            ,root: 'results'
        })
        ,mode: 'remote'
    });
    Campaigner.combo.ARFields.superclass.constructor.call(this,config);
};
Ext.extend(Campaigner.combo.ARFields,MODx.combo.ComboBox);
Ext.reg('campaigner-combo-arfields', Campaigner.combo.ARFields);