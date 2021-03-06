<?php

 /*               DO NOT EDIT THIS FILE

  Edit the file in the MyComponent config directory
  and run ExportObjects

 */



 /*               DO NOT EDIT THIS FILE

  Edit the file in the MyComponent config directory
  and run ExportObjects

 */



$packageNameLower = 'campaigner'; /* No spaces, no dashes */

$components = array(
    /* These are used to define the package and set values for placeholders */
    'packageName' => 'campaigner',  /* No spaces, no dashes */
    'packageNameLower' => $packageNameLower,
    'packageDescription' => 'Campaigner is MODx component built to distribute emails aka sending newsletters',
    'version' => '1.0.0',
    'release' => 'beta1',
    'author' => 'Subsolutions',
    'email' => '<http://www.subsolutions.at>',
    'authorUrl' => 'http://www.subsolutions.at',
    'authorSiteName' => "Subsolutions",
    'packageDocumentationUrl' => '',
    'copyright' => '2013',

    /* no need to edit this except to change format */
    'createdon' => strftime('%m-%d-%Y'),

    'gitHubUsername' => 'herooutoftime',
    'gitHubRepository' => 'campaigner',

    /* two-letter code of your primary language */
    'primaryLanguage' => 'en',

    /* Set directory and file permissions for project directories */
    'dirPermission' => 0755,  /* No quotes!! */
    'filePermission' => 0644, /* No quotes!! */

    /* Define source and target directories (mycomponent root and core directories) */
    'mycomponentRoot' => $this->modx->getOption('mc.root', null,
        MODX_CORE_PATH . 'components/mycomponent/'),
    /* path to MyComponent source files */
    'mycomponentCore' => $this->modx->getOption('mc.core_path', null,
        MODX_CORE_PATH . 'components/mycomponent/core/components/campaigner/'),
    /* path to new project root */
    'targetRoot' => MODX_ASSETS_PATH . 'mycomponents/' . $packageNameLower . '/',


    /* *********************** NEW SYSTEM SETTINGS ************************ */

    /* If your extra needs new System Settings, set their field values here.
     * You can also create or edit them in the Manager (System -> System Settings),
     * and export them with exportObjects. If you do that, be sure to set
     * their namespace to the lowercase package name of your extra */

    'newSystemSettings' => array(
        // Mailing
        'campaigner.mail_charset' => array( // key
            'key' => 'campaigner.mail_charset',
            'name' => 'Campaigner Mail Charset',
            'description' => 'Campaigner Mail Charset',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'UTF-8',
            'area' => 'mailing',
        ),
        'campaigner.mail_encoding' => array( // key
            'key' => 'campaigner.mail_encoding',
            'name' => 'Campaigner Mail Encoding',
            'description' => 'Campaigner Mail Encoding',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '8bit',
            'area' => 'mailing',
        ),
        'campaigner.tracking_page' => array( // key
            'key' => 'campaigner.tracking_page',
            'name' => 'Campaigner Tracking Page',
            'description' => 'Campaigner Tracking Page',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'mailing',
        ),
        'campaigner.tracking_enabled' => array( // key
            'key' => 'campaigner.tracking_enabled',
            'name' => 'Campaigner Enable Tracking',
            'description' => 'Campaigner Enable Tracking',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'mailing',
        ),
        'campaigner.unsubscribe_reasons' => array( // key
            'key' => 'campaigner.unsubscribe_reasons',
            'name' => 'Campaigner Unsubscribe reasons',
            'description' => 'Campaigner Unsubscribe reasons<br/>MODx input option value like string: \'Display==value||Display2==value2\'',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'No interest==interest||Too much content==content_big||Content boring==content_boring',
            'area' => 'mailing',
        ),
        'campaigner.tac' => array( // key
            'key' => 'campaigner.tac',
            'name' => 'Campaigner Terms & Conditions',
            'description' => 'Campaigner Terms and Conditions are required',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'mailing',
        ),

        // System
        'campaigner.newsletter.autosend' => array( // key
            'key' => 'campaigner.newsletter.autosend',
            'name' => 'Campaigner Newsletter Autosend',
            'description' => 'Campaigner Newsletter Autosend',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => false,
            'area' => 'system',
        ),

        'campaigner.mail_smtp_auth' => array( // key
            'key' => 'campaigner.mail_smtp_auth',
            'name' => 'Campaigner SMTP Authentication',
            'description' => 'Campaigner SMTP Authentication',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => false,
            'area' => 'system',
        ),

        'campaigner.mail_smtp_helo' => array( // key
            'key' => 'campaigner.mail_smtp_helo',
            'name' => 'Campaigner SMTP HELO',
            'description' => 'Campaigner SMTP HELO',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_hosts' => array( // key
            'key' => 'campaigner.mail_smtp_hosts',
            'name' => 'Campaigner SMTP Host',
            'description' => 'Campaigner SMTP Host',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'localhost',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_keepalive' => array( // key
            'key' => 'campaigner.mail_smtp_keepalive',
            'name' => 'Campaigner SMTP Keep-Alive',
            'description' => 'Campaigner SMTP Keep-Alive',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'system',
        ),
        'campaigner.mail_smtp_pass' => array( // key
            'key' => 'campaigner.mail_smtp_pass',
            'name' => 'Campaigner SMTP Password',
            'description' => 'Campaigner SMTP Password',
            'namespace' => 'campaigner',
            'xtype' => 'text-password',
            'inputType' => 'password',
            'value' => '',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_port' => array( // key
            'key' => 'campaigner.mail_smtp_port',
            'name' => 'Campaigner SMTP Port',
            'description' => 'Campaigner SMTP Port',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '25',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_prefix' => array( // key
            'key' => 'campaigner.mail_smtp_prefix',
            'name' => 'Campaigner SMTP Prefix',
            'description' => 'Campaigner SMTP Prefix',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_timeout' => array( // key
            'key' => 'campaigner.mail_smtp_timeout',
            'name' => 'Campaigner SMTP Timeout',
            'description' => 'Campaigner SMTP Timeout',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '10',
            'area' => 'system',
        ),
        'campaigner.mail_smtp_user' => array( // key
            'key' => 'campaigner.mail_smtp_user',
            'name' => 'Campaigner SMTP User',
            'description' => 'Campaigner SMTP User',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'system',
        ),
        'campaigner.batchsize' => array( // key
            'key' => 'campaigner.batchsize',
            'name' => 'Campaigner Batchsize',
            'description' => 'Campaigner Batchsize',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '100',
            'area' => 'system',
        ),
        'campaigner.default_groups' => array( // key
            'key' => 'campaigner.default_groups',
            'name' => 'Campaigner Group (Default)',
            'description' => 'Campaigner Group (Default)',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '3',
            'area' => 'system',
        ),
        
        'campaigner.default_from' => array( // key
            'key' => 'campaigner.default_from',
            'name' => 'Campaigner From (Default)',
            'description' => 'Campaigner From (Default)',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'you@domain.com',
            'area' => 'system',
        ),
        'campaigner.default_name' => array( // key
            'key' => 'campaigner.default_name',
            'name' => 'Campaigner Name (Default)',
            'description' => 'Campaigner Name (Default)',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'You',
            'area' => 'system',
        ),
        'campaigner.return_path' => array( // key
            'key' => 'campaigner.return_path',
            'name' => 'Campaigner Reply-To',
            'description' => 'Campaigner Reply-To',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => 'rt@domain.com',
            'area' => 'system',
        ),
        'campaigner.test_mail' => array( // key
            'key' => 'campaigner.test_mail',
            'name' => 'Campaigner Test Mail Address',
            'description' => 'Campaigner Test Mail Address',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'system',
        ),
        'campgaigner.has_autonewsletter' => array( // key
            'key' => 'campgaigner.has_autonewsletter',
            'name' => 'Campaigner has Autonewsletters',
            'description' => 'Campaigner with Autonewsletters',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'system',
        ),
        'campgaigner.system_mails' => array( // key
            'key' => 'campgaigner.system_mails',
            'name' => 'Campaigner System Mails',
            'description' => 'Campaigner System Mails',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'system',
        ),
        'campgaigner.system_mails.addresses' => array( // key
            'key' => 'campgaigner.system_mails.addresses',
            'name' => 'Campaigner System Mails Addresses',
            'description' => 'Campaigner System Mails Addresses',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'system',
        ),
        //Structure
        'campaigner.autonewsletter_folder' => array( // key
            'key' => 'campaigner.autonewsletter_folder',
            'name' => 'Campaigner Autonewsletter Folder',
            'description' => 'Campaigner Autonewsletter Folder',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        'campaigner.confirm_mail' => array( // key
            'key' => 'campaigner.confirm_mail',
            'name' => 'Campaigner Confirm Mail Resource',
            'description' => 'Campaigner Confirm Mail Resource',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        'campaigner.confirm_page' => array( // key
            'key' => 'campaigner.confirm_page',
            'name' => 'Campaigner Confirm Page Resource',
            'description' => 'Campaigner Confirm Page Resource',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        'campaigner.newsletter_folder' => array( // key
            'key' => 'campaigner.newsletter_folder',
            'name' => 'Campaigner Newsletter Folder',
            'description' => 'Campaigner Autonewsletter Folder',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        'campaigner.newsletter_subfolders' => array( // key
            'key' => 'campaigner.newsletter_subfolders',
            'name' => 'Campaigner Newsletter Sub-Folder',
            'description' => 'Campaigner Newsletter Sub-Folder',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => true,
            'area' => 'site',
        ),
        'campaigner.unsubscribe_page' => array( // key
            'key' => 'campaigner.unsubscribe_page',
            'name' => 'Campaigner Unsubscribe Page',
            'description' => 'Campaigner Unsubscribe Page',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        'campaigner.default_template' => array( // key
            'key' => 'campaigner.default_template',
            'name' => 'Campaigner Default Template',
            'description' => 'Campaigner Default Template',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'site',
        ),
        // File
        'campaigner.attachment_tv' => array( // key
            'key' => 'campaigner.attachment_tv',
            'name' => 'Campaigner Attachment TV',
            'description' => 'Campaigner Attachment TV',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'file',
        ),
        // Language
        'campaigner.salutation' => array( // key
            'key' => 'campaigner.salutation',
            'name' => 'Campaigner Salutation',
            'description' => 'Campaigner Salutation',
            'namespace' => 'campaigner',
            'xtype' => 'textfield',
            'value' => '',
            'area' => 'language',
        ),

        // Automatic
        'campaigner.autofill' => array( // key
            'key' => 'campaigner.autofill',
            'name' => 'Campaigner Autofill',
            'description' => 'Campaigner Autofill',
            'namespace' => 'campaigner',
            'xtype' => 'combo-boolean',
            'value' => false,
            'area' => 'automatic',
        ),
    ),

    /* ************************ NEW SYSTEM EVENTS ************************* */

    /* Array of your new System Events (not default
     * MODX System Events). Listed here so they can be created during
     * install and removed during uninstall.
     *
     * Warning: Do *not* list regular MODX System Events here !!! */

    // 'newSystemEvents' => array(
    //     'OnMyEvent1' => array(
    //         'name' => 'OnMyEvent1',
    //     ),
    //     'OnMyEvent2' => array(
    //         'name' => 'OnMyEvent2',
    //         'groupname' => 'campaigner',
    //         'service' => 1,
    //     ),
    // ),

    /* ************************ NAMESPACE(S) ************************* */
    /* (optional) Typically, there's only one namespace which is set
     * to the $packageNameLower value. Paths should end in a slash
    */

    'namespaces' => array(
        'campaigner' => array(
            'name' => 'campaigner',
            'path' => '{core_path}components/campaigner/',
            'assets_path' => '{assets_path}components/campaigner/',
        ),

    ),

    /* ************************ CONTEXT(S) ************************* */
    /* (optional) List any contexts other than the 'web' context here
    */

    // 'contexts' => array(
    //     'campaigner' => array(
    //         'key' => 'campaigner',
    //         'description' => 'campaigner context',
    //         'rank' => 2,
    //     )
    // ),

    /* *********************** CONTEXT SETTINGS ************************ */

    /* If your extra needs Context Settings, set their field values here.
     * You can also create or edit them in the Manager (Edit Context -> Context Settings),
     * and export them with exportObjects. If you do that, be sure to set
     * their namespace to the lowercase package name of your extra.
     * The context_key should be the name of an actual context.
     * */

    // 'contextSettings' => array(
    //     'campaigner_context_setting1' => array(
    //         'context_key' => 'campaigner',
    //         'key' => 'campaigner_context_setting1',
    //         'name' => 'campaigner Setting One',
    //         'description' => 'Description for setting one',
    //         'namespace' => 'campaigner',
    //         'xtype' => 'textfield',
    //         'value' => 'value1',
    //         'area' => 'campaigner',
    //     ),
    //     'campaigner_context_setting2' => array(
    //         'context_key' => 'campaigner',
    //         'key' => 'campaigner_context_setting2',
    //         'name' => 'campaigner Setting Two',
    //         'description' => 'Description for setting two',
    //         'namespace' => 'campaigner',
    //         'xtype' => 'combo-boolean',
    //         'value' => true,
    //         'area' => 'campaigner',
    //     ),
    // ),

    /* ************************* CATEGORIES *************************** */
    /* (optional) List of categories. This is only necessary if you
     * need to categories other than the one named for packageName
     * or want to nest categories.
    */

    'categories' => array(
        'campaigner' => array(
            'category' => 'campaigner',
            'parent' => '',  /* top level category */
        ),
        // 'category2' => array(
        //     'category' => 'Category2',
        //     'parent' => 'campaigner', /* nested under campaigner */
        // )
    ),

    /* *************************** MENUS ****************************** */

    /* If your extra needs Menus, you can create them here
     * or create them in the Manager, and export them with exportObjects.
     * Be sure to set their namespace to the lowercase package name
     * of your extra.
     *
     * Every menu should have exactly one action */

    'menus' => array(
        'campaigner' => array(
            'text' => 'campaigner',
            'parent' => 'components',
            'description' => 'campaigner.desc',
            'icon' => '',
            'menuindex' => 0,
            'params' => '',
            'handler' => '',
            'permissions' => '',

            'action' => array(
                'id' => '',
                'namespace' => 'campaigner',
                'controller' => 'index',
                'haslayout' => true,
                'lang_topics' => 'campaigner:default',
                'assets' => '',
            ),
        ),
    ),


    /* ************************* ELEMENTS **************************** */

    /* Array containing elements for your extra. 'category' is required
       for each element, all other fields are optional.
       Property Sets (if any) must come first!

       The standard file names are in this form:
           SnippetName.snippet.php
           PluginName.plugin.php
           ChunkName.chunk.html
           TemplateName.template.html

       If your file names are not standard, add this field:
          'filename' => 'actualFileName',
    */


    'elements' => array(

        // 'propertySets' => array( /* all three fields are required */
        //     'PropertySet1' => array(
        //         'name' => 'PropertySet1',
        //         'description' => 'Description for PropertySet1',
        //         'category' => 'campaigner',
        //     ),
        //     'PropertySet2' => array(
        //         'name' => 'PropertySet2',
        //         'description' => 'Description for PropertySet2',
        //         'category' => 'campaigner',
        //     ),
        // ),

        'snippets' => array(
            'CampaignerConfirm' => array(
                'category' => 'campaigner',
                'description' => 'Confirms a subscription',
                'static' => false,
            ),
            'CampaignerSubscribe' => array( /* campaigner with static and property set(s)  */
                'category' => 'campaigner',
                'description' => 'Subscribe snippet',
                'static' => false,
                
            ),
            'CampaignerUnsubscribe' => array( /* campaigner with static and property set(s)  */
                'category' => 'campaigner',
                'description' => 'Unsubscribe snippet',
                'static' => false,
                
            ),
        ),
        'plugins' => array(
            'CampaignerResource' => array( /* campaigner with static, events, and property sets */
                'category' => 'campaigner',
                'description' => 'Creates campaignes when events are hit',
                'static' => false,
                'events' => array(
                    'OnDocFormSave' => array(),
                    'OnBeforeEmptyTrash' => array(),
                ),
            ),
            'CampaignerTracking' => array( /* campaigner with static, events, and property sets */
                'category' => 'campaigner',
                'description' => 'Tracks clicks & opens of subscribers',
                'static' => false,
                'events' => array(
                    'OnLoadWebDocument' => array(),
                ),
            ),
        ),
        'chunks' => array(
            'CampaignerForm' => array(
                'category' => 'campaigner',
            ),
            'CampaignerFormSimple' => array(
                'category' => 'campaigner',
            ),
            'CampaignerMessage' => array(
                'category' => 'campaigner',
            ),
            'CampaignerCheckbox' => array(
                'category' => 'campaigner',
            ),
            'CampaignerOption' => array(
                'category' => 'campaigner',
            ),
            'CampaignerFormUnsubscribe' => array(
                'category' => 'campaigner',
            ),
        ),
        'templates' => array(
            'CampaignerTemplate' => array(
                'category' => 'campaigner',
            ),
        ),
        //     'Template2' => array(
        //         'category' => 'campaigner',
        //         'description' => 'Description for Template two',
        //         'static' => false,
        //         'propertySets' => array(
        //             'PropertySet2',
        //         ),
        //     ),
        // ),
        'templateVars' => array(
            'tvCampaignerAttachments' => array(
                'category' => 'campaigner',
                'caption' => 'Attachments',
            ),
            'tvCampaignerData' => array(
                'category' => 'campaigner',
                'caption' => 'Data',
                'type' => 'hidden',
            ),
            'tvCampaignerSent' => array(
                'category' => 'campaigner',
                'caption' => 'Sent',
                'type' => 'hidden',
            ),
        ),
    ),
    /* (optional) will make all element objects static - 'static' field above will be ignored */
    'allStatic' => false,


    /* ************************* RESOURCES ****************************
     Important: This list only affects Bootstrap. There is another
     list of resources below that controls ExportObjects.
     * ************************************************************** */
    /* Array of Resource pagetitles for your Extra; All other fields optional.
       You can set any resource field here */
    // 'resources' => array(
    //     'Resource1' => array( /* minimal campaigner */
    //         'pagetitle' => 'Campaigner subscription',
    //         'alias' => 'campaigner-subscription',
    //         'context_key' => 'web',
    //         'published' => true,
    //         'content' => '[[!CampaignerSubscribe? &groups=`3`]]'
    //     ),
    //     'Resource2' => array( /* campaigner with other fields */
    //         'pagetitle' => 'Campaigner unsubscribe',
    //         'alias' => 'campaigner-unsubscribe',
    //         'context_key' => 'web',
    //         'published' => true,
    //         'content' => '[[!CampaignerUnsubscribe]]'
    //     ),
    //     'Resource3' => array( /* campaigner with other fields */
    //         'pagetitle' => 'Campaigner confirm',
    //         'alias' => 'campaigner-confirm',
    //         'context_key' => 'web',
    //         'published' => true,
    //         'content' => '[[!CampaignerConfirm]]'
    //     )
    // ),


    /* Array of languages for which you will have language files,
     *  and comma-separated list of topics
     *  ('.inc.php' will be added as a suffix). */
    'languages' => array(
        'en' => array(
            'default',
        ),
        'de' => array(
            'default',
        )
    ),
    /* ********************************************* */
    /* Define optional directories to create under assets.
     * Add your own as needed.
     * Set to true to create directory.
     * Set to hasAssets = false to skip.
     * Empty js and/or css files will be created.
     */
    'hasAssets' => true,
    // 'minifyJS' => true,
    /* minify any JS files */
    'assetsDirs' => array(
        'css' => true,
        /* If true, a default (empty) CSS file will be created */
        'js' => true,
        /* If true, a default (empty) JS file will be created */
        'images' => true,
        'audio' => false,
        'video' => false,
        'themes' => false,
        'imports' => true,
    ),


    /* ********************************************* */
    /* Define basic directories and files to be created in project*/

    'docs' => array(
        'readme.txt',
        'license.txt',
        'changelog.txt',
        'tutorial.html'
    ),

    /* (optional) Description file for GitHub project home page */
    'README.md' => true,
    /* assume every package has a core directory */
    'hasCore' => true,

    /* ********************************************* */
    /* (optional) Array of extra script resolver(s) to be run
     * during install. Note that resolvers to connect plugins to events,
     * property sets to elements, resources to templates, and TVs to
     * templates will be created automatically -- *don't* list those here!
     *
     * 'default' creates a default resolver named after the package.
     * (other resolvers may be created above for TVs and plugins).
     * Suffix 'resolver.php' will be added automatically */
    'resolvers' => array(
        'default',
        'addUsers',
        'tables',
        'policy',
        'fcs',
    ),

    /* (optional) Validators can abort the install after checking
     * conditions. Array of validator names (no
     * prefix of suffix) or '' 'default' creates a default resolver
     *  named after the package suffix 'validator.php' will be added */

    'validators' => array(
        'default',
        'hasGdLib'
    ),

    /* (optional) install.options is needed if you will interact
     * with user during the install.
     * See the user.input.php file for more information.
     * Set this to 'install.options' or ''
     * The file will be created as _build/install.options/user.input.php
     * Don't change the filename or directory name. */
    // 'install.options' => 'install.options',


    /* Suffixes to use for resource and element code files (not implemented)  */
    'suffixes' => array(
        'modPlugin' => '.php',
        'modSnippet' => '.php',
        'modChunk' => '.html',
        'modTemplate' => '.html',
        'modResource' => '.html',
    ),


    /* ********************************************* */
    /* (optional) Only necessary if you will have class files.
     *
     * Array of class files to be created.
     *
     * Format is:
     *
     * 'ClassName' => 'directory:filename',
     *
     * or
     *
     *  'ClassName' => 'filename',
     *
     * ('.class.php' will be appended automatically)
     *
     *  Class file will be created as:
     * yourcomponent/core/components/yourcomponent/model/[directory/]{filename}.class.php
     *
     * Set to array() if there are no classes. */
    'classes' => array(
        'campaigner' => 'campaigner:campaigner',
    ),

    /* *******************************************
     * These settings control exportObjects.php  *
     ******************************************* */
    /* ExportObjects will update existing files. If you set dryRun
       to '1', ExportObjects will report what it would have done
       without changing anything. Note: On some platforms,
       dryRun is *very* slow  */

    'dryRun' => '0',

    /* Array of elements to export. All elements set below will be handled.
     *
     * To export resources, be sure to list pagetitles and/or IDs of parents
     * of desired resources
    */
    'process' => array(
        'contexts',
        'snippets',
        'plugins',
        'templateVars',
        'templates',
        'chunks',
        'resources',
        'propertySets',
        'systemSettings',
        'contextSettings',
        'systemEvents',
        'menus'
    ),
    /*  Array  of resources to process. You can specify specific resources
        or parent (container) resources, or both.

        They can be specified by pagetitle or ID, but you must use the same method
        for all settings and specify it here. Important: use IDs if you have
        duplicate pagetitles */
    'getResourcesById' => false,

    // 'exportResources' => array(
    //     'Resource1',
    //     'Resource2',
    // ),
    /* Array of resource parent IDs to get children of. */
    'parents' => array(),
    /* Also export the listed parent resources
      (set to false to include just the children) */
    'includeParents' => false,


    /* ******************** LEXICON HELPER SETTINGS ***************** */
    /* These settings are used by LexiconHelper */
    'rewriteCodeFiles' => false,
    /*# remove ~~descriptions */
    'rewriteLexiconFiles' => true,
    /* automatically add missing strings to lexicon files */
    /* ******************************************* */

     /* Array of aliases used in code for the properties array.
     * Used by the checkproperties utility to check properties in code against
     * the properties in your properties transport files.
     * if you use something else, add it here (OK to remove ones you never use.
     * Search also checks with '$this->' prefix -- no need to add it here. */
    'scriptPropertiesAliases' => array(
        'props',
        'sp',
        'config',
'scriptProperties'
        ),
);

return $components;
