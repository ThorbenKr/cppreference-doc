<?php
/**
 * Vector - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

#require_once(dirname(__FILE__)."/cppreference/resourcemodules.php");

/**
 * SkinTemplate class for Cppreference skin
 * @ingroup Skins
 */
class SkinCppreference extends SkinTemplate {

	var $skinname = 'cppreference', $stylename = 'cppreference',
		$template = 'CppreferenceTemplate', $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath, $wgRequest;

		parent::initPage( $out );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $wgRequest->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
		);

		$out->addModuleScripts( 'skins.cppreference' );
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ){
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'skins.cppreference' );
	}
}

/**
 * QuickTemplate class for Cppreference skin
 * @ingroup Skins
 */
class CppreferenceTemplate extends BaseTemplate {

	/* Members */

	/**
	 * @var Skin Cached skin object
	 */
	var $skin;

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgLang, $wgVectorUseIconWatch;

		$this->skin = $this->data['skin'];

		// Build additional attributes for navigation urls
		//$nav = $this->skin->buildNavigationUrls();
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->skin->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
		}
		// Output HTML Page
		$this->html( 'headelement' );
?>
        <div id="cpp-page-base">
            <!-- header -->
            <div id="mw-head" class="noprint">
                <div id="cpp-head-first">
                    <h5><a href="/"><?php global $wgSitename; echo $wgSitename;?></a></h5>
                    <div id="cpp-head-search">
                        <?php $this->renderNavigation( 'SEARCH' ); ?>
                    </div>
                    <div id="cpp-head-personal">
                        <?php $this->renderNavigation( 'PERSONAL' ); ?>
                    </div>

                </div>
                <div id="cpp-head-second">
                    <div id="cpp-head-tools-left">
                        <?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS' ) ); ?>
                    </div>
                    <div id="cpp-head-tools-right">
                        <?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS' ) ); ?>
                    </div>
                </div>
            </div>
            <!-- /header -->
            <!-- content -->
            <div id="content">
                <a id="top"></a>
                <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
                <?php if ( $this->data['sitenotice'] ): ?>
                <!-- sitenotice -->
                <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
                <!-- /sitenotice -->
                <?php endif; ?>
                <!-- firstHeading -->
                <h1 id="firstHeading" class="firstHeading"><?php $this->html( 'title' ) ?></h1>
                <!-- /firstHeading -->
                <!-- bodyContent -->
                <div id="bodyContent">
                    <?php if ( $this->data['isarticle'] ): ?>
                    <!-- tagline -->
                    <div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
                    <!-- /tagline -->
                    <?php endif; ?>
                    <!-- subtitle -->
                    <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
                    <!-- /subtitle -->
                    <?php if ( $this->data['undelete'] ): ?>
                    <!-- undelete -->
                    <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
                    <!-- /undelete -->
                    <?php endif; ?>
                    <?php if( $this->data['newtalk'] ): ?>
                    <!-- newtalk -->
                    <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
                    <!-- /newtalk -->
                    <?php endif; ?>
                    <!-- bodycontent -->
                    <?php $this->html( 'bodycontent' ) ?>
                    <!-- /bodycontent -->
                    <?php if ( $this->data['printfooter'] ): ?>
                    <!-- printfooter -->
                    <div class="printfooter">
                    <?php $this->html( 'printfooter' ); ?>
                    </div>
                    <!-- /printfooter -->
                    <?php endif; ?>
                    <?php if ( $this->data['catlinks'] ): ?>
                    <!-- catlinks -->
                    <?php $this->html( 'catlinks' ); ?>
                    <!-- /catlinks -->
                    <?php endif; ?>
                    <?php if ( $this->data['dataAfterContent'] ): ?>
                    <!-- dataAfterContent -->
                    <?php $this->html( 'dataAfterContent' ); ?>
                    <!-- /dataAfterContent -->
                    <?php endif; ?>
                    <div class="visualClear"></div>
                    <!-- debughtml -->
                    <?php $this->html( 'debughtml' ); ?>
                    <!-- /debughtml -->
                </div>
                <!-- /bodyContent -->
            </div>
            <!-- /content -->
            <!-- footer -->
            <div id="footer"<?php $this->html( 'userlangattributes' ) ?>>
                <?php $this->renderBottomNavigation();?>
                <?php $this->renderToolbox(); ?>
                <?php $this->renderFooter(); ?>
            </div>
            <!-- /footer -->
        </div>
        <?php $this->printTrail(); ?>
    </body>
</html>
<?php
	}

    private function renderToolbox()
    {
        $name = 'tb';
        
        $content = $this->getToolbox();

        $msg = 'toolbox';
        $msg_obj = wfMessage( $msg );
        $message = htmlspecialchars($msg_obj->exists() ? $msg_obj->text() : $msg);
        
        ?>
        <div id="cpp-toolbox">
            <h5><span><?php echo $message; ?></span><a href="#"></a></h5>
            <ul>
<?php       foreach( $content as $key => $val ):
                echo $this->makeListItem( $key, $val );
            endforeach;
            wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this, true ));
            ?>
            </ul>
        </div>
<?php
    }
    
    private function renderBottomNavigation()
    {
        $content = $this->data['sidebar']['navigation'];

        $msg = 'navigation';
        $msg_obj = wfMessage( $msg );
        $message = htmlspecialchars($msg_obj->exists() ? $msg_obj->text() : $msg);
        
        ?>
        <div id="cpp-navigation">
            <h5><?php echo $message; ?></h5>
            <ul>
<?php       foreach( $content as $key => $val ):
                echo $this->makeListItem( $key, $val );
            endforeach; ?>
            </ul>
        </div>
<?php
    }
    
    private function renderLanguages()
    {
        $content = $this->data['language_urls'];

        $msg = 'otherlanguages';
        $msg_obj = wfMessage( $msg );
        $message = htmlspecialchars($msg_obj->exists() ? $msg_obj->text() : $msg);
        
        ?>
        <div id="cpp-languages">
            <div><ul><li><?php echo $message; ?></li></ul></div>
            <div><ul>
<?php       foreach( $content as $key => $val ):
                echo $this->makeListItem( $key, $val );
            endforeach; ?>
            </ul></div>
        </div>
<?php
    }

    private function renderFooter()
    {
        if ( $this->data['language_urls'] ) { $this->renderLanguages(); }
        foreach( $this->getFooterLinks() as $category => $links ): ?>
            <ul id="footer-<?php echo $category ?>">
                <?php foreach( $links as $link ): ?>
                    <li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
        <?php $footericons = $this->getFooterIcons("icononly");
                if ( count( $footericons ) > 0 ): ?>
                    <ul id="footer-icons" class="noprint">
            <?php   foreach ( $footericons as $blockName => $footerIcons ): ?>
                        <li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
                <?php   foreach ( $footerIcons as $icon ): ?>
                            <?php echo $this->skin->makeFooterIcon( $icon ); ?>
                <?php   endforeach; ?>
                        </li>
            <?php   endforeach; ?>
                    </ul>
        <?php   endif; ?>
                <div style="clear:both">
            </div>
<?php
    }

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $wgLang->isRTL() ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			echo "\n<!-- {$name} -->\n";
			switch ( $element ) {
				case 'NAMESPACES':
?>
<div id="p-namespaces" class="vectorTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><?php $this->msg( 'namespaces' ) ?></h5>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<?php if ( $wgVectorShowVariantName ): ?>
		<h4>
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
				<?php echo htmlspecialchars( $link['text'] ) ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</h4>
	<?php endif; ?>
	<h5><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
?>
<div id="p-views" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>">
	<h5><?php $this->msg('views') ?></h5>
	<ul<?php $this->html('userlangattributes') ?>>
		<?php foreach ( $this->data['view_urls'] as $link ): ?>
			<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'ACTIONS':
?>
<div id="p-cactions" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><span><?php $this->msg( 'actions' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'PERSONAL':
?>
<div id="p-personal" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
<?php
        $tools = $this->getPersonalTools();
        $item = reset($tools);
        $key = key($tools);
        array_shift($tools);

        echo $this->makeListItem( $key, $item, array( 'tag' => 'span' ) );
        if ( count( $tools ) > 0 ) {
?>
	<div class="menu">
        <ul<?php $this->html( 'userlangattributes' ) ?>>
<?php               foreach( $tools as $key => $item ) { 
                        echo $this->makeListItem( $key, $item ); 
                    } ?>
        </ul>
    </div>
<?php   } ?>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search">
	<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		<?php if ( true ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-rtl.png' ) ) ); ?>
			<?php endif; ?>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'text' ) ); ?>
			<?php if ( !$this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-ltr.png' ) ) ); ?>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
		<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
		<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		<?php endif; ?>
	</form>
</div>
<?php

				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
}
