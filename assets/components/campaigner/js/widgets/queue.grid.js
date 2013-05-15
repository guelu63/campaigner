Campaigner.grid.Queue = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        url: Campaigner.config.connector_url
        ,baseParams: { action: 'mgr/queue/getList', showProcessed: 0 }
        ,fields: ['id', 'subscriber', 'newsletter', 'state', 'subject', 'date', 'firstname', 'lastname', 'text', 'email', 'sent', 'bounced', 'total', 'priority']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,sm: this.sm
        ,columns: [
            this.sm,
        {
            header: _('campaigner.queue.newsletter')
            ,dataIndex: 'newsletter'
            ,sortable: true
            ,width: 30
            ,renderer: this._renderNewsletter
        },{
            header: _('campaigner.queue.receiver')
            ,dataIndex: 'subscriber'
            ,sortable: true
            ,width: 40
            ,renderer: this._renderSubscriber
        },{
            header: _('campaigner.queue.state')
            ,dataIndex: 'state'
            ,sortable: true
            ,width: 10
            ,renderer: this._renderState
        },{
            header: _('campaigner.queue.priority')
            ,dataIndex: 'priority'
            ,sortable: true
            ,width: 10
        },{
            header: _('campaigner.queue.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 20
        }],
        /* Top toolbar */
        tbar : [{

            xtype: 'splitbutton'
            ,text: _('campaigner.queue.batch_actions')
            // ,handler: this.cleanerMedia.createDelegate(this, [{cleaner: 1}])
            // ,tooltip: {text:'This is a an example QuickTip for a toolbar item', title:'Tip Title'}
            // Menus can be built/referenced by using nested menu config objects
            ,menu : {
                items: [{
                    text: _('campaigner.queue.remove_marked')
                    ,handler: this.removeQueue
                    ,scope : this
                }, {
                    text: _('campaigner.queue.send_marked')
                    ,handler: this.processQueue
                    ,scope : this
                }]
            }
        }, {
            xtype: 'button'
            ,id: 'campaigner-filter-processed'
            ,text: _('campaigner.queue.show_processed')
            ,listeners: {
                'click': {fn: this.toggleProcessed, scope: this}
            }
        },'|',{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'campaigner-filter-search'
            ,emptyText: _('search')+'...'
            ,listeners: {
                'change': {fn: this.filterSearch, scope: this}
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
        },
        '->',
        {
            xtype: 'button'
            ,id: 'campaigner-process-queue'
            ,text: _('campaigner.queue.process_queue')
            ,listeners: {
                'click': {fn: this.processQueue, scope: this}
            }
        },
        {
            xtype: 'button'
            ,id: 'campaigner-remove-tests'
            ,text: _('campaigner.queue.remove_tests')
            ,listeners: {
                'click': {fn: this.removeTests, scope: this}
            }
        }]
    });
    Campaigner.grid.Queue.superclass.constructor.call(this,config);
};

Ext.extend(Campaigner.grid.Queue,MODx.grid.Grid,{
    _renderNewsletter: function(value, p, rec) {
        return rec.data.subject;
    }
    ,_renderSubscriber: function(value, p, rec) {
        return '<span class="subscriber">' + rec.data.email + '</span> (' + value + ')';
    }
    ,_renderState: function(value, p, rec) {
        if(value == 1)
            return '<img src="'+ Campaigner.config.base_url +'images/sent.png" alt="' + _('campaigner.queue.sent') + '" />';
        return '<img src="'+ Campaigner.config.base_url +'images/waiting.png" alt="' + _('campaigner.queue.waiting') + '" />';
    }
    ,toggleProcessed: function(btn, e) {
        var s = this.getStore();
        if (btn.text ==  _('campaigner.queue.show_processed')) {
            s.setBaseParam('showProcessed',1);
            btn.setText(_('campaigner.queue.hide_processed'));
        } else {
            s.setBaseParam('showProcessed',0);
            btn.setText(_('campaigner.queue.show_processed'));
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,removeQueue: function(e) {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}
        // var msg;
        // if(this.menu.record.state === 0) {
        //     msg = _('campaigner.queue.remove.unsend');
        // } else {
        //     msg = _('campaigner.queue.remove.confirm');
        // }
        MODx.msg.confirm({
            title: _('campaigner.queue.remove.title')
            ,text: _('campaigner.queue.remove_info')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/remove'
                ,marked: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,processQueue: function() {
        var cs = this.getSelectedAsList();
        if (cs === false) {return false;}
        MODx.msg.confirm({
            title: _('campaigner.queue.process_queue')
            ,text: _('campaigner.queue.process_queue_text')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/process'
                ,marked: cs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,removeTests: function() {
        MODx.msg.confirm({
            title: _('campaigner.queue.remove_tests')
            ,text: _('campaigner.queue.remove_tests_text')
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/remove_tests'
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,getMenu: function() {
        var m = [];
        if (this.getSelectionModel().getCount() == 1) {
            var rs = this.getSelectionModel().getSelections();
            m.push({
                text: _('campaigner.queue.remove')
                ,handler: this.removeQueue
            });
            m.push({
                text: _('campaigner.queue.send')
                ,handler: this.processQueue
            });
        }
        if (m.length > 0) {
            this.addContextMenuItem(m);
        }
    }
    ,filterSearch: function(tf,newValue,oldValue) {
        var nv = newValue;
        this.getStore().baseParams.search = nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
});
Ext.reg('campaigner-grid-queue',Campaigner.grid.Queue);
