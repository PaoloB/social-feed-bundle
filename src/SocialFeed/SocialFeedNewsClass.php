<?php

namespace Pdir\SocialFeedBundle\SocialFeed;

class SocialFeedNewsClass {

    public function parseNews($objTemplate, $arrRow, $objModule)
    {
        if($arrRow['social_feed_id'] != "") {

            if($objModule->pdir_sf_text_length > 0) {
                $teaser = $arrRow['teaser'];
                $more = "";
                if( strlen($teaser) > $objModule->pdir_sf_text_length) {
                    $more = " ...";
                }
                $teaser = \StringUtil::substrHtml($teaser,$objModule->pdir_sf_text_length).$more;
            } else {
                $teaser = $arrRow['teaser'];
            }

            $objTemplate->sfTextLength = $objModule->pdir_sf_text_length;
            $objTemplate->sfElementWidth = $objModule->pdir_sf_columns;
            $objTemplate->sfImages = $objModule->pdir_sf_enableImages;
            $objTemplate->sfFbLink = $arrRow['pdir_sf_fb_link'];
            $objTemplate->sfFbAccountPicture = $arrRow['social_feed_account_picture'];
            $objTemplate->sfFbAccount = $arrRow['social_feed_account'];
            $objTemplate->teaser = $teaser;
            $objTemplate->socialFeedType = $arrRow['social_feed_type'];
        }
    }

}