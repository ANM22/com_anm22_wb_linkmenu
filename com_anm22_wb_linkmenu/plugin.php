<?php

/**
 * Link menu plugin for ANM22 WebBase
 *
 * @author Andrea Menghi <andrea.menghi@anm22.it>
 */
class com_anm22_wb_linkmenu extends com_anm22_wb_editor_page_element {

    var $elementClass = "com_anm22_wb_linkmenu";
    var $elementPlugin = "com_anm22_wb_linkmenu";
    var $linkMenu = [];
    var $cssClass;
    var $mobileMenu;

    /**
     * @deprecated since editor 3.0
     * 
     * Method to init the element.
     * 
     * @param SimpleXMLElement $xml Element data
     * @return void
     */
    function importXMLdoJob($xml)
    {
        $tmpListRoot = htmlspecialchars_decode($xml->code);
        $this->mobileMenu = $xml->mobileMenu;
        $tmp = explode(";", $tmpListRoot);
        if (count($tmp) > 0) {
            foreach ($tmp as $t) {
                $items = [];
                if ($t != "") {
                    $tmp2 = explode(",", trim($t));
                    if (count($tmp2) > 0) {
                        foreach ($tmp2 as $s) {
                            array_push($items, trim($s));
                        }
                    }
                    array_push($this->linkMenu, $items);
                    unset($items);
                }
            }
        }
        $this->cssClass = $xml->cssClass;
    }

    /**
     * Method to init the element.
     * 
     * @param mixed[] $data Element data
     * @return void
     */
    public function initData($data)
    {
        $tmpListRoot = htmlspecialchars_decode($data['code']);
        $this->mobileMenu = $data['mobileMenu'];
        $tmp = explode(";", $tmpListRoot);
        if (count($tmp) > 0) {
            foreach ($tmp as $t) {
                $items = [];
                if ($t != "") {
                    $tmp2 = explode(",", trim($t));
                    if (count($tmp2) > 0) {
                        foreach ($tmp2 as $s) {
                            array_push($items, trim($s));
                        }
                    }
                    array_push($this->linkMenu, $items);
                    unset($items);
                }
            }
        }
        
        $this->cssClass = htmlspecialchars_decode($data['cssClass']);
    }

    /**
     * Render the page element
     * 
     * @return void
     */
    function show()
    {
        ?><script>
            $(function () {
                var linkListAnimDuration = 100;
                var linkListButtonClass = ".link-list-button-<?= $this->id ?>";  /*Generazione ID casuale per ogni componente DA FARE*/
                var linkListMobileMenuId = "#link-list-mobile-ul-<?= $this->id ?>";  /*Generazione ID casuale per ogni componente*/
                var linkListDesktopMenuId = "#link-list-desktop-ul-<?= $this->id ?>";  /*Generazione ID casuale per ogni componente*/
                var mobileMenu = "<?= $this->mobileMenu . "-" . $this->id ?>";
                var mobileLink = ".mobile-link-<?= $this->id ?>";
                var desktopLink = ".desktop-link-<?= $this->id ?>";
                var selectedClass = "selected";

                var linkListWidthLimit;

                switch (mobileMenu) {
                    case "auto" + "-" + "<?= $this->id ?>":
                        linkListWidthLimit = 790;
                        break;
                    case "always" + "-" + "<?= $this->id ?>":
                        linkListWidthLimit = 10000;
                        break;
                    case "never" + "-" + "<?= $this->id ?>":
                        linkListWidthLimit = 0;
                        break;
                }

                /*Nel caso di click sul mobile: se è aperto chiudi, se no apri*/
                $(linkListButtonClass).click(function (e) {
                    if ($(linkListMobileMenuId).css("display") == "none")
                        $(linkListMobileMenuId).slideDown(linkListAnimDuration);
                    else
                        $(linkListMobileMenuId).slideUp(linkListAnimDuration);
                });
                /*Nel caso in cui lo schermo sia più stretto di un certo x, fai sparire il desktop menu e apparire il button, ma non il menu mobile, sia all'inizio che sul resize*/
                /*Nel caso in cui lo schermo sia più largo di un certo x, fai sparire il mobile e apparire il desktop, sia all'inizio che sul resize*/
                if ($(window).width() <= linkListWidthLimit) {
                    $(linkListDesktopMenuId).css("display", "none");
                    $(linkListMobileMenuId).css("display", "none");
                    $(linkListButtonClass).css("display", "block");
                } else {
                    $(linkListDesktopMenuId).css("display", "inline-block");
                    $(linkListMobileMenuId).css("display", "none");
                    $(linkListButtonClass).css("display", "none");
                }

                $(window).resize(function (e) {
                    if ($(window).width() <= linkListWidthLimit) {
                        $(linkListDesktopMenuId).css("display", "none");
                        $(linkListMobileMenuId).css("display", "none");
                        $(linkListButtonClass).css("display", "block");
                    } else {
                        $(linkListDesktopMenuId).css("display", "inline-block");
                        $(linkListMobileMenuId).css("display", "none");
                        $(linkListButtonClass).css("display", "none");
                    }
                });

                $(mobileLink).click(function () {
                    $(linkListMobileMenuId).slideUp(linkListAnimDuration);
                    $(mobileLink).each(function (elem) {
                        $(elem).parent().removeClass(selectedClass);
                    });
                    $(desktopLink).each(function (elem) {
                        $(elem).parent().removeClass(selectedClass);
                    });
                    $(this).parent().addClass(selectedClass);
                });

                $(desktopLink).click(function () {
                    $(mobileLink).each(function (index, elem) {
                        $(elem).parent().removeClass(selectedClass);
                    });
                    $(desktopLink).each(function (index, elem) {
                        $(elem).parent().removeClass(selectedClass);
                    });
                    $(this).parent().addClass(selectedClass);
                });

            });
        </script><?
        echo '<div class="';
                if ($this->cssClass != "") {
                    echo $this->elementClass . "_" . $this->elementPlugin . " " . $this->cssClass;
                } else {
                    echo $this->elementClass . "_" . $this->elementPlugin;
                }
                echo '">';
            echo '<nav id="link-list-desktop-ul-' . $this->id . '" class="link-list-desktop-ul">'; // Inizio UL Desktop
                echo '<ul>';
                    if (count($this->linkMenu) > 0) {
                        foreach ($this->linkMenu as $it) {
                            echo '<li id="' . $it[2] . '"><a class="desktop-link-' . $this->id . '" href="';
                                    if ($it[1]) {
                                        echo $it[1];
                                    } else {
                                        echo "javascript:void(0)";
                                    }
                                    echo '">' . $it[0] . '</a></li>';
                        }
                    }
                echo '</ul>';
            echo '</nav>';  // Fine Desktop

            /* menu mobile button */
            echo '<div class="link-list-button link-list-button-' . $this->id . '"></div>';

            echo '<nav id="link-list-mobile-ul-' . $this->id . '" class="link-list-mobile-ul" style="display:none;">'; // Inizio Nav Mobile
                echo '<ul>';
                    if (count($this->linkMenu) > 0) {
                        foreach ($this->linkMenu as $it) {
                            echo '<li id="' . $it[2] . '">';
                                echo '<a class="mobile-link-' . $this->id . '" href="';
                                    if ($it[1]) {
                                        echo $it[1];
                                    } else {
                                        echo "javascript:void(0)";
                                    }
                                    echo '">' . $it[0] . '</a></li>';
                        }
                    }
                echo '</ul>';
            echo '</nav>'; // Fine Mobile
        echo '</div>'; // Fine Div Plugin
    }

}