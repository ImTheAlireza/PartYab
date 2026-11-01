<?php
/**
 * Hero car map with hotspot links to the four main categories.
 *
 * @package PartYab
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hero-visual">
        <div class="car-map">
          <div class="car-map-head">
            <b><?php partyab_the_icon( 'car', 'icon' ); ?> نقشهٔ قطعات خودرو</b>
            <span>روی هر بخش بزنید</span>
          </div>
          <div class="car-stage">
            <div class="car-wrap">
            <svg class="car-svg" viewBox="44 44 552 250" role="img" aria-label="نقشهٔ بخش‌های خودرو">
              <path class="ground" d="M24 256H616"/>
              <path class="body" d="M118 226C88 226 66 218 62 200L58 184C56 168 64 158 82 154L148 142C158 140 166 136 174 128L208 78C218 64 232 58 248 58H348C364 58 378 64 388 76L432 120C440 129 450 133 462 135L530 144C558 148 574 160 576 180L578 198C580 216 566 226 540 226Z"/>
              <path class="glass" d="M232 66H210L186 104V132H232Z"/>
              <path class="glass" d="M244 66H330V132H244Z"/>
              <path class="glass" d="M342 66H384L420 108V132H342Z"/>
              <path class="line" d="M238 132V224M336 132V224"/>
              <path class="detail" d="M258 156H276M348 156H366"/>
              <path class="detail" d="M540 204H578M62 204H118"/>
              <rect class="detail" x="554" y="156" width="20" height="13" rx="4"/>
              <rect class="detail" x="76" y="157" width="15" height="11" rx="3"/>
              <path class="detail" d="M424 119l15-5 3 8-14 4z"/>
              <circle class="wheel" cx="470" cy="222" r="33"/>
              <circle class="rim" cx="470" cy="222" r="14"/>
              <path class="rim" d="M470 208v-6M470 236v6M456 222h-6M484 222h6"/>
              <g class="dim">
                <path d="M170 268h300"/>
                <path d="M170 262v12M470 262v12"/>
                <text x="320" y="286" text-anchor="middle">WHEELBASE 2470 mm</text>
              </g>
              <circle class="wheel" cx="170" cy="222" r="33"/>
              <circle class="rim" cx="170" cy="222" r="14"/>
              <path class="rim" d="M170 208v-6M170 236v6M156 222h-6M184 222h6"/>
            </svg>

            <a class="hotspot" style="--x:23%;--y:71%" href="<?php echo esc_url( partyab_cat_url( 'brake' ) ); ?>" aria-label="ترمز و ABS">
              <span class="hs-dot"></span><span class="hs-label">ترمز و ABS</span>
            </a>
            <a class="hotspot" style="--x:77%;--y:71%" href="<?php echo esc_url( partyab_cat_url( 'suspension' ) ); ?>" aria-label="جلوبندی و تعلیق">
              <span class="hs-dot"></span><span class="hs-label">جلوبندی</span>
            </a>
            <a class="hotspot" style="--x:25%;--y:38%" href="<?php echo esc_url( partyab_cat_url( 'filter' ) ); ?>" aria-label="فیلتر و روغن">
              <span class="hs-dot"></span><span class="hs-label">فیلتر و روغن</span>
            </a>
            <a class="hotspot" style="--x:10%;--y:44%" href="<?php echo esc_url( partyab_cat_url( 'electrical' ) ); ?>" aria-label="برق و روشنایی">
              <span class="hs-dot"></span><span class="hs-label">برق و روشنایی</span>
            </a>
            </div>
          </div>
          <div class="map-legend">
            <a href="<?php echo esc_url( partyab_cat_url( 'brake' ) ); ?>"><i></i>ترمز</a>
            <a href="<?php echo esc_url( partyab_cat_url( 'suspension' ) ); ?>"><i></i>جلوبندی</a>
            <a href="<?php echo esc_url( partyab_cat_url( 'filter' ) ); ?>"><i></i>فیلتر و روغن</a>
            <a href="<?php echo esc_url( partyab_cat_url( 'electrical' ) ); ?>"><i></i>برق و روشنایی</a>
          </div>
          <div class="car-map-foot">
            <span><b>۴</b> دستهٔ اصلی روی نقشه</span>
            <span>انتخاب خودرو، فیلتر خودکار</span>
          </div>
        </div>
      </div>
    </div>
