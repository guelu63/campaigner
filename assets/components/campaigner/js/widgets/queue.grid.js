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
        ,columns: [{
            header: _('campaigner.queue.newsletter')
            ,dataIndex: 'newsletter'
            ,sortable: true
            ,width: 40
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
            ,width: 5
        },{
            header: _('campaigner.queue.sent')
            ,dataIndex: 'sent'
            ,sortable: true
            ,width: 20
        }],  
         /* Top toolbar */  
         tbar : [{
            xtype: 'button'
            ,id: 'campaigner-filter-processed'
            ,text: _('campaigner.queue.show_processed')
            ,listeners: {
                'click': {fn: this.toggleProcessed, scope: this}
            }
	 } , '|' , {
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
        }]
    });
    Campaigner.grid.Queue.superclass.constructor.call(this,config)
};

Ext.extend(Campaigner.grid.Queue,MODx.grid.Grid,{
    _renderNewsletter: function(value, p, rec) {
	return rec.data.subject;
    }
    ,_renderSubscriber: function(value, p, rec) {
	return '<span class="subscriber">' + rec.data.email + '</span> (' + value + ')';
    }
    ,_renderState: function(value, p, rec) {
	if(value == 1) {
	    return '<img src="'+ Campaigner.config.base_url +'images/sent.png" alt="' + _('campaigner.queue.sent') + '" />';
	}
	return '<img src="'+ Campaigner.config.base_url +'images/waiting.png" alt="' + _('campaigner.queue.waiting') + '" />';
    }
    ,toggleProcessed: function(btn, e) {
        var s = this.getStore();
        if (btn.text ==  _('campaigner.queue.show_processed')) {
            s.setBaseParam('showProcessed',1);
	    btn.setText(_('campaigner.queue.hide_processed'))
        } else {
            s.setBaseParam('showProcessed',0);
	    btn.setText(_('campaigner.queue.show_processed'))
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,removeQueue: function(e) {
	var msg;
	if(this.menu.record.state == 0) {
	    msg = _('campaigner.queue.remove.unsend');
	} else {
	    msg = _('campaigner.queue.remove.confirm');
	}
        MODx.msg.confirm({
            title: _('campaigner.queue.remove.title')
            ,text: msg
            ,url: Campaigner.config.connector_url
            ,params: {
                action: 'mgr/queue/remove'
                ,id: this.menu.record.id
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
