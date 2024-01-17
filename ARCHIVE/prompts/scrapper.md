the following js script :
```
const express = require('express');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const app = express();
const port = 3001;app.use(express.json());
async function checkLastPostForCompany(page, postSelector, author) {
const posts = await page.$$(postSelector);
const lastPost = posts[posts.length - 1];const textContent = await lastPost.evaluate(el => el.innerText);
if (textContent.includes(author)) {
return true;
}const allAttributes = await lastPost.evaluate(el => {
const attributes = Array.from(el.attributes);
return attributes.map(attr => attr.value);
});for (const attr of allAttributes) {
if (attr.includes(author)) {
return true;
}
}return false;
}app.post('/scrape', async (req, res) => {
const { secret_key, url, selectorsArray, attributesArray, namesArray, postSelector } = req.body;
if (secret_key !== 'test') {
return res.status(401).json({ error: 'Unauthorized' });
}try {
const browser = await puppeteer.launch();
const page = await browser.newPage();
await page.goto(url, { timeout: 60000 });let results = [];
let posts = [];
while (true) {
await page.evaluate(() => {
window.scrollBy(0, window.innerHeight);
});try {
await page.waitForTimeout(3000);
} catch (error) {
break;
}let currentHeight = await page.evaluate('document.body.scrollHeight');
let viewportHeight = await page.evaluate('window.innerHeight');
let scrollPosition = await page.evaluate('window.scrollY');
condition = await checkLastPostForCompany(page,postSelector,"Alpine Laser")if (currentHeight <= viewportHeight + scrollPosition || condition == false) {
console.log(condition)
posts = await page.$$(postSelector);
console.log(`Found ${posts.length} posts.`);
break;
}}for (const post of posts) {
const itemData = {};for (let i = 0; i < selectorsArray.length; i++) {
const selector = selectorsArray[i];
const attribute = attributesArray[i];
const name = namesArray[i];try {
const elements = await post.$$(selector);
let values = [];for (let element of elements) {
let value;if (attribute === 'innerText') {
value = await element.evaluate(el => el.innerText);
} else {
value = await element.evaluate((el, attr) => el.getAttribute(attr), attribute);
}if (value && value.trim() !== '') {
values.push(value);
}
}if (values.length > 0) {
itemData[name] = values;
}
} catch (error) {
console.error(`Error retrieving data for selector "${selector}": ${error.message}`);
}
}if (Object.keys(itemData).length > 0 && !results.some(result => JSON.stringify(result) === JSON.stringify(itemData))) {
results.push(itemData);
}
}await browser.close();
res.json({ results });} catch (error) {
console.error(error);
res.status(500).json({ error: 'Internal Server Error' });
}
});app.listen(port, () => {
console.log(`Server is listening on port ${port}`);
});
```
returns :
```
{"results":[{"URN":["urn:li:activity:7117516266000498688"],"author":["Alpine
Laser"],"age":["3mo"],"reactions":["25"],"comments":["2
Comments"],"images":["https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_2048_1536/0/1696948113674?e=2147483647&v=beta&t=pUJztCmDCuirwcqkXm-eocdA4vDRh3ui20rHkAb44JQ"]},{"URN":["urn:li:activity:7110664133217288192"],"author":["Alpine
Laser"],"age":["3mo"],"reactions":["119"],"comments":["8
Comments"],"images":["https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_2048_1536/0/1695314437358?e=2147483647&v=beta&t=g53yY2_Unlp0wnDGnS_TTEDTrW0NGHcRX0S1CIklHFo","https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_2048_1536/0/1695314437404?e=2147483647&v=beta&t=-b88XXslHvHLphceYz5JlbzeG7YYVmINZXIqThqWBWc","https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_2048_1536/0/1695314436890?e=2147483647&v=beta&t=ih0r1LCV_ZB-sXDRP1AOVc5loPEWN2jJEKXZbFeWEMw","https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_2048_1536/0/1695314437501?e=2147483647&v=beta&t=6IKGVD8z2EC-m8uVPJOZHu2x54fTs47_EL2QBoIIAPU","https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_2048_1536/0/1695314437783?e=2147483647&v=beta&t=yIfktpvMC9WXX7mGKnJYtxwu5ayWztFyqWDEjIBNFRE"]},{"URN":["urn:li:activity:7105606901618417664"],"author":["Alpine
Laser"],"age":["4mo"],"reactions":["14"]},{"URN":["urn:li:activity:7092583182209875968"],"author":["Alpine
Laser"],"age":["5mo"],"reactions":["30"],"images":["https://media.licdn.com/dms/image/D5622AQElkuOrteJbWg/feedshare-shrink_2048_1536/0/1691003603743?e=2147483647&v=beta&t=uzUu8eXOZj-hV1obM_xqJ_1EpIzgsr8ndXA-jajzBKw"]},{"URN":["urn:li:activity:7090069626461532160"],"author":["Alpine
Laser"],"age":["5mo"],"reactions":["85"],"comments":["4
Comments"],"images":["https://media.licdn.com/dms/image/D4E22AQH9L9hhXmwLhg/feedshare-shrink_2048_1536/0/1690404325095?e=2147483647&v=beta&t=1leGT7yzfLGC_qx0XcdwO4Uvg_DfL_Dl6lazWFU97lA"]},{"URN":["urn:li:activity:7085263372841041920"],"author":["Alpine
Laser"],"age":["6mo"],"reactions":["119"],"comments":["6
Comments"],"images":["https://media.licdn.com/dms/image/D4D22AQGqLOmYU5zQJQ/feedshare-shrink_800/0/1689258424335?e=2147483647&v=beta&t=-6rp9Xf0Dcc19OP2pS9WSY1ZPvpR2byC4o3LTNJOsB0","https://media.licdn.com/dms/image/D4D22AQFjeXMtn0ZgcQ/feedshare-shrink_800/0/1689258424269?e=2147483647&v=beta&t=ulWCIS13Zekx0uRavaizBfl0bhN3KbcH-_E0hUx9Abo","https://media.licdn.com/dms/image/D4D22AQECZgYGzGDO6g/feedshare-shrink_800/0/1689258424307?e=2147483647&v=beta&t=nHve-1OHtoYZYZERG1sUN98Lo8W9xILLjzSsY30ZhOM","https://media.licdn.com/dms/image/D4D22AQEMYp-_RwB6hA/feedshare-shrink_800/0/1689258424267?e=2147483647&v=beta&t=TY1moYnXxNF9VEEiZ3qGfZ2pEyoa9eQJAT4xgOyAU4w"]},{"URN":["urn:li:activity:7084633761740423169"],"author":["Alpine
Laser"],"age":["6mo"],"reactions":["108"],"comments":["5
Comments"],"images":["https://media.licdn.com/dms/image/D5622AQE0uiOv1X59Og/feedshare-shrink_800/0/1689108312570?e=2147483647&v=beta&t=MhKywaOzFT-SfrK2DcERSUPNjXYukm46axKvQu96SBw","https://media.licdn.com/dms/image/D5622AQEDvNoAXKgCkA/feedshare-shrink_800/0/1689108308231?e=2147483647&v=beta&t=Dl1rGOTpPop83hp06wo5UIduYNMOoYFT_lZsBAl8iVs","https://media.licdn.com/dms/image/D5622AQGuLM3G0lYTmQ/feedshare-shrink_800/0/1689108310054?e=2147483647&v=beta&t=OrMIghhybZy-O4eSc7ij9zpqeROskH_ny_Af4mBl_Lg","https://media.licdn.com/dms/image/D5622AQEs3FWPkEZ4fg/feedshare-shrink_800/0/1689108313262?e=2147483647&v=beta&t=gz73kyrnFVcsz7-JgvO0Q8gE0F1OfTlMVJw6lncpG0I","https://media.licdn.com/dms/image/D5622AQGwIi2isOxGuQ/feedshare-shrink_800/0/1689108311592?e=2147483647&v=beta&t=aepOpoDEmiQz4zPfmX46gcfPtZwnKjvK8ramekwurxE"]},{"URN":["urn:li:activity:7084620236297015296","urn:li:activity:7084250167482200064"],"age":["6mo"],"reactions":["41"],"images":["https://media.licdn.com/dms/image/D5622AQE8KLX-4zEhng/feedshare-shrink_2048_1536/0/1689016857644?e=2147483647&v=beta&t=CwL7rBPo9NzfIv8wAG_1hQnFADFyE5O53ea8xuef48g"]},{"URN":["urn:li:activity:7047668554556420096","urn:li:activity:7046919667239575552"],"age":["9mo"],"reactions":["106"],"comments":["2
Comments"]},{"URN":["urn:li:activity:7038554054553210881","urn:li:activity:7038533440094289924"],"age":["10mo
Edited"],"reactions":["192"],"comments":["10
Comments"]},{"URN":["urn:li:activity:7033849074818707456","urn:li:activity:7033832487432724480"],"age":["10mo"],"images":["https://media.licdn.com/dms/image/C5622AQE76HZbjrDrwQ/feedshare-shrink_800/0/1676996341740?e=2147483647&v=beta&t=5sa2zdmbhXK330Ou3SOuRo5C15QABd0YF5iGDPLxWVc"]},{"URN":["urn:li:activity:7033826103584579584","urn:li:activity:7032378885606424577"],"age":["11mo
Edited"]},{"URN":["urn:li:activity:7032643995075760128"],"age":["11mo"]},{"URN":["urn:li:activity:7027299780204580864","urn:li:activity:7026934813685075969"],"age":["11mo
Edited"],"images":["https://media.licdn.com/dms/image/C4E22AQF7ei6oYSOCHw/feedshare-shrink_2048_1536/0/1675351812793?e=2147483647&v=beta&t=IMD1lbiHGMtd0lDGMb52zmxuN81bMZxQq01oalr16I4"]},{"URN":["urn:li:activity:7023741456741777408"],"age":["11mo"],"images":["https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=2147483647&v=beta&t=jpm_qxl2hxtHLU2nIaHONFKdHT7oF_2CrLpR38FeQtk","https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=2147483647&v=beta&t=E83UT1nFkyqx001U2UcoWxivMH8zkX4snlVv-uEjr-U","https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=2147483647&v=beta&t=CHr8prYsGvekN8iqV51sasSkCDILAjg-pSssm3THvoY","https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=2147483647&v=beta&t=o6OCUI3Nk29QRqA3Qvm53A8oQmxzTqRGEW2zz_nzpVo"]}]}
```

when run with those params:
```
'linkedin_scrapper_full_post_selector' => 'li[class="mb-1"]',
'linkedin_scrapper_full_selectors_array' => [
'li[class="mb-1"] article',
"a[data-tracking-control-name='organization_guest_main-feed-card_feed-actor-name']",
'time',
'a[data-tracking-control-name="public_post_feed-actor-image"] img',
'p[data-test-id="main-feed-activity-card_commentary"]',
'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-reactions"]',
'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-comments"]',
'ul[data-test-id="feed-images-content"] img'
],
'linkedin_scrapper_full_attributes_array' =>
'["data-activity-urn","innerText","innerText","src","innerText","innerText","innerText","src"]',
'linkedin_scrapper_full_names_array' => '["URN","author","age","profilePicture","copy","reactions"
,"comments","images"]',
```


### issue:
the script with those parameters doesn't return all values for every post 
- the script doesn't return the post copy for any of the posts
- here is a post example source where the script doesn't return reactions, comments, author, profilePicture nor copy:
```
<li class="mb-1">
<div class="relative" data-id="entire-feed-card-link">
<a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card__full-link" data-id="main-feed-card__full-link" href="https://www.linkedin.com/posts/alpine-laser_femtosecond-workstation-spotlight-activity-7023741456741777408-1YuV" class="absolute left-0 top-0 h-full w-full z-1 !border-0">
</a><article data-id="main-feed-card" data-attributed-urn="urn:li:share:7023741455798079488" data-featured-activity-urn="urn:li:activity:7023741456741777408" data-activity-urn="urn:li:activity:7023741456741777408" class="relative pt-1.5 px-2 pb-0 bg-color-background-container
container-lined main-feed-activity-card papabear:max-w-[550px]" data-is-initialized-feed-item="true" data-feed-item-list-position="15"><div data-test-id="main-feed-activity-card__entity-lockup" class="flex items-center font-sans mb-1">
<a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_feed-actor-image" class="relative" href="https://www.linkedin.com/company/alpine-laser?trk=org_guest_main-feed-card_feed-actor-image"><img alt="View organization page for Alpine Laser" data-ghost-url="https://static.licdn.com/aero-v1/sc/h/cs8pjfgyw96g44ln9r7tct85f" data-ghost-classes="bg-color-entity-ghost-background" class="inline-block relative w-6 h-6 lazy-loaded" aria-busy="false" src="https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&amp;v=beta&amp;t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"></a>
<div class="flex flex-col self-start min-w-0 ml-1">
<div class="text-color-text">
<a data-tracking-will-navigate="" aria-label="View organization page for Alpine Laser" data-tracking-control-name="org_guest_main-feed-card_feed-actor-name" href="https://www.linkedin.com/company/alpine-laser?trk=org_guest_main-feed-card_feed-actor-name" class="text-sm link-styled no-underline leading-open">
Alpine Laser
</a>
</div><p class="!text-xs text-color-text-low-emphasis leading-[1.33333] m-0 truncate">
549 followers
</p><span class="!text-xs text-color-text-low-emphasis leading-[1.33333] flex">
<time class="flex-none">
11mo</time>
</span>
</div></div>
<div data-test-id="main-feed-activity-card__ellipsis-menu" class="ellipsis-menu absolute right-0 top-0 !mr-0.5">
<div class="collapsible-dropdown flex items-center relative hyphens-auto"><button data-tracking-control-name="org_guest_main-feed-card_ellipsis-menu-trigger" aria-label="Open menu" aria-expanded="false" class="ellipsis-menu__trigger
collapsible-dropdown__button btn-md btn-tertiary cursor-pointer
!py-[6px] !px-1 flex items-center rounded-[50%]">
<icon class="ellipsis-menu__trigger-icon m-0 p-0 centered-icon lazy-loaded" aria-hidden="true" aria-busy="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" data-supported-dps="24x24" fill="currentColor" focusable="false" class="lazy-loaded" aria-busy="false">
<path d="M2 10h4v4H2v-4zm8 4h4v-4h-4v4zm8-4v4h4v-4h-4z"></path>
</svg></icon>
</button>
<ul tabindex="-1" role="menu" class="collapsible-dropdown__list hidden container-raised absolute w-auto overflow-y-auto flex-col items-stretch z-1 bottom-auto top-[100%]"><li class="ellipsis-menu__item border-t-1 border-solid border-color-border-low-emphasis first-of-type:border-none flex"><a class="semaphore__toggle visited:text-color-text-secondary ellipsis-menu__semaphore ellipsis-menu__item-button flex items-center w-full p-1 cursor-pointer font-sans text-sm font-bold link-styled focus:link-styled link:no-underline active:bg-color-background-container-tint focus:bg-color-background-container-tint hover:bg-color-background-container-tint outline-offset-[-2px]" data-modal="semaphore__toggle" data-is-logged-in="false" data-semaphore-tracking-prefix="org_guest_main-feed-card_ellipsis-menu-semaphore" data-semaphore-content-urn="urn:li:activity:7023741456741777408" data-semaphore-content-type="POST" data-item-type="semaphore" data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_ellipsis-menu-semaphore-sign-in-redirect" href="/uas/login?trk=org_guest_main-feed-card_ellipsis-menu-semaphore-sign-in-redirect&amp;guestReportContentType=POST&amp;_f=guest-reporting"><icon class="ellipsis-menu__item-icon text-color-text h-[24px] w-[24px] mr-1 lazy-loaded" aria-hidden="true" aria-busy="false">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" data-supported-dps="24x24" fill="currentColor" focusable="false" class="lazy-loaded" aria-busy="false">
<path d="M13.82 5L14 4a1 1 0 00-1-1H5V2H3v20h2v-7h4.18L9 16a1 1 0 001 1h8.87L21 5h-7.18zM5 13V5h6.94l-1.41 8H5zm12.35 2h-6.3l1.42-8h6.29z"></path>
</svg></icon>
Report this post</a>
</li>
</ul>
</div>
</div>
<div class="attributed-text-segment-list__container relative mt-1 mb-1.5 babybear:mt-0 babybear:mb-0.5">
<p data-test-id="main-feed-activity-card__commentary" dir="ltr" class="attributed-text-segment-list__content text-color-text !text-sm whitespace-pre-wrap break-words">* Femtosecond Workstation Spotlight *- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source
- Hollow Core Fiber Delivery with Active Beam Management 
- Laser control module and laser head unit mounted within the machine base
- Available in both programmable 2 and 4 axis configurationsInquire to learn more at <a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card-text" rel="nofollow" target="_self" href="mailto:sales@alpinelaser.com?trk=org_guest_main-feed-card-text" class="link">sales@alpinelaser.com</a>&nbsp;</p>
</div>
<ul data-test-id="feed-images-content" class="grid grid-cols-6 grid-rows-[60%_40%] w-main-feed-card-media feed-images-content list-none pl-0gap-0.125 h-[425px] babybear:h-[360px]">
<li data-position="0" data-test-id="feed-images-content__list-item" class="bg-color-background-container-tint
col-span-full">
<img alt="Alpine Laser MediCut PRO, Femtosecond Laser, Ultrafast Laser, Tube Cutting Workstation, Laser Tube Cutting" data-ghost-url="https://static.licdn.com/aero-v1/sc/h/921ynb2z53z33mhm3061qvjag" class="w-full object-cover object-center h-full babybear:max-h-[400px] lazy-loaded" aria-busy="false" src="https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=2147483647&amp;v=beta&amp;t=jpm_qxl2hxtHLU2nIaHONFKdHT7oF_2CrLpR38FeQtk">
</li>
<li data-position="1" data-test-id="feed-images-content__list-item" class="bg-color-background-container-tint
col-span-2">
<img alt="Alpine Laser MediCut PRO, Femtosecond Laser, Ultrafast Laser, Tube Cutting Workstation, Laser Tube Cutting" data-ghost-url="https://static.licdn.com/aero-v1/sc/h/921ynb2z53z33mhm3061qvjag" class="w-full object-cover object-center h-full babybear:max-h-[400px] lazy-loaded" aria-busy="false" src="https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=2147483647&amp;v=beta&amp;t=E83UT1nFkyqx001U2UcoWxivMH8zkX4snlVv-uEjr-U">
</li>
<li data-position="2" data-test-id="feed-images-content__list-item" class="bg-color-background-container-tint
col-span-2">
<img alt="Alpine Laser MediCut PRO, Femtosecond Laser, Ultrafast Laser, Tube Cutting Workstation, Laser Tube Cutting" data-ghost-url="https://static.licdn.com/aero-v1/sc/h/921ynb2z53z33mhm3061qvjag" class="w-full object-cover object-center h-full babybear:max-h-[400px] lazy-loaded" aria-busy="false" src="https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=2147483647&amp;v=beta&amp;t=CHr8prYsGvekN8iqV51sasSkCDILAjg-pSssm3THvoY">
</li>
<li data-position="3" data-test-id="feed-images-content__list-item" class="bg-color-background-container-tint
col-span-2">
<img alt="Alpine Laser MediCut PRO, Femtosecond Laser, Ultrafast Laser, Tube Cutting Workstation, Laser Tube Cutting" data-ghost-url="https://static.licdn.com/aero-v1/sc/h/921ynb2z53z33mhm3061qvjag" class="w-full object-cover object-center h-full babybear:max-h-[400px] lazy-loaded" aria-busy="false" src="https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=2147483647&amp;v=beta&amp;t=o6OCUI3Nk29QRqA3Qvm53A8oQmxzTqRGEW2zz_nzpVo">
</li>
</ul>
<div class="flex items-center font-sans text-sm babybear:text-xs main-feed-activity-card__social-actions"><a data-plural="%numReactions%" data-singular="%numReactions%" data-num-reactions="28" data-id="social-actions__reactions" data-test-id="social-actions__reactions" data-separate-ctas="false" aria-label="28 Reactions" class="flex items-center font-normal text-color-text-low-emphasis no-underline visited:text-color-text-low-emphasismy-1" data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_social-actions-reactions" target="_self" href="https://www.linkedin.com/signup?session_redirect=https%3A%2F%2Fwww%2Elinkedin%2Ecom%2Fcompany%2Falpine-laser&amp;trk=org_guest_main-feed-card_social-actions-reactions"><img width="16px" height="16px" data-reaction-type="LIKE" alt="" aria-busy="false" src="https://static.licdn.com/aero-v1/sc/h/9sun4diznbhgyelaeryxy75ur" class="lazy-loaded">
<img width="16px" height="16px" data-reaction-type="APPRECIATION" alt="" aria-busy="false" src="https://static.licdn.com/aero-v1/sc/h/cib97epu77mrzque5gqlzjgaw" class="lazy-loaded"><span data-test-id="social-actions__reaction-count" class="font-normal ml-0.5">
28
</span></a><code style="display: none" id="social-actions__reaction-image-APPRECIATION"><!--"https://static.licdn.com/aero-v1/sc/h/cib97epu77mrzque5gqlzjgaw"--></code>
<code style="display: none" id="social-actions__reaction-image-EMPATHY"><!--"https://static.licdn.com/aero-v1/sc/h/37hg81qnm85dsy5mbqfifg9qs"--></code>
<code style="display: none" id="social-actions__reaction-image-ENTERTAINMENT"><!--"https://static.licdn.com/aero-v1/sc/h/e7ppwfmo0if15fgie4q5r4us4"--></code>
<code style="display: none" id="social-actions__reaction-image-INTEREST"><!--"https://static.licdn.com/aero-v1/sc/h/4v7dh2d6cuqm24mzps1pqzeqt"--></code>
<code style="display: none" id="social-actions__reaction-image-LIKE"><!--"https://static.licdn.com/aero-v1/sc/h/9sun4diznbhgyelaeryxy75ur"--></code>
<code style="display: none" id="social-actions__reaction-image-MAYBE"><!--"https://static.licdn.com/aero-v1/sc/h/4iy169rwfs5rdhspekg2r5wc6"--></code>
<code style="display: none" id="social-actions__reaction-image-PRAISE"><!--"https://static.licdn.com/aero-v1/sc/h/cjrxeu0ese8oxe32kiom8kzoh"--></code></div><div class="social-action-bar flex flex-wrap border-t-1 border-solid border-color-border-faint min-h-[44px] items-center babybear:justify-around !m-0"><a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_like-cta" href="https://www.linkedin.com/signup?session_redirect=https%3A%2F%2Fwww%2Elinkedin%2Ecom%2Fcompany%2Falpine-laser&amp;trk=org_guest_main-feed-card_like-cta" class="social-action-bar__button !rounded-none">
<icon data-svg-class-name="social-action-bar__icon--svg" class="social-action-bar__icon lazy-loaded" aria-hidden="true" aria-busy="false">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" focusable="false" class="social-action-bar__icon--svg lazy-loaded" aria-busy="false"><path d="M19.5 11l-3.9-3.9c-.8-.8-1.3-1.7-1.7-2.7l-.5-1.5C13 1.8 11.9 1 10.8 1 9.2 1 8 2.2 8 3.8v1.1c0 1 .2 1.9.5 2.8L8.9 9H4.1C3 9 2 10 2 11.1c0 .7.4 1.4.9 1.8-.6.4-.9 1-.9 1.7 0 .9.5 1.6 1.3 2-.2.3-.3.7-.3 1 0 1.1.9 2.1 2 2.1v.1C5 21 6 22 7.1 22h7.5c1.2 0 2.5-.3 3.6-.8l.3-.2H21V11h-1.5zm-.5 8h-1l-.7.4c-.8.4-1.8.6-2.7.6H7.7c-.4 0-.8-.3-1-.7l-.3-.9-.8-.4c-.4-.1-.7-.6-.6-1l.2-1-.8-.7c-.3-.4-.4-.9-.1-1.3l.7-1.1-.7-1.1c-.3-.4-.1-.8.3-.8h7l-1.3-3.9c-.2-.7-.3-1.5-.3-2.2V3.8c0-.5.3-.8.8-.8.3 0 .6.2.7.5L12 5c.4 1.3 1.2 2.5 2.2 3.5l4.5 4.5h.3v6z" fill="currentColor"></path></svg></icon>
<span class="social-action-bar__button-text">
Like
</span>
</a>
<a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_comment-cta" href="https://www.linkedin.com/signup?session_redirect=https%3A%2F%2Fwww%2Elinkedin%2Ecom%2Fcompany%2Falpine-laser&amp;trk=org_guest_main-feed-card_comment-cta" class="social-action-bar__button !rounded-none">
<icon data-svg-class-name="social-action-bar__icon--svg" class="social-action-bar__icon lazy-loaded" aria-hidden="true" aria-busy="false">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" focusable="false" class="social-action-bar__icon--svg lazy-loaded" aria-busy="false"><path d="M7 9h10v1H7V9zm0 4h7v-1H7v1zm16-2c0 2.2-1 4.3-2.8 5.6L12 22v-4H8c-3.9 0-7-3.1-7-7s3.1-7 7-7h8c3.9 0 7 3.1 7 7zm-2 0c0-2.8-2.2-5-5-5H8c-2.8 0-5 2.2-5 5s2.2 5 5 5h6v2.3l5-3.3c1.3-.9 2-2.4 2-4z" fill="currentColor"></path></svg></icon>
<span class="social-action-bar__button-text">
Comment
</span>
</a><a data-tracking-will-navigate="" data-tracking-control-name="org_guest_main-feed-card_share-cta" href="https://www.linkedin.com/signup?session_redirect=https%3A%2F%2Fwww%2Elinkedin%2Ecom%2Fcompany%2Falpine-laser&amp;trk=org_guest_main-feed-card_share-cta" class="social-action-bar__button !rounded-none">
<icon data-svg-class-name="social-action-bar__icon--svg" class="social-action-bar__icon lazy-loaded" aria-hidden="true" aria-busy="false">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" focusable="false" class="social-action-bar__icon--svg lazy-loaded" aria-busy="false"><path d="M18.4 5H16l3.9 6H8c-3.4 0-6 2.8-6 5.9 0 .6.1 1.2.3 1.8L3 21h2.1l-.9-2.8c-.1-.4-.2-.8-.2-1.2 0-2.1 1.6-4 3.9-4h12L16 19h2.4l4.6-7-4.6-7z" fill="currentColor"></path></svg></icon>
<span class="social-action-bar__button-text">
Share
</span>
</a>
</div>
</article>
<code style="display: none" id="is-mobile"><!--false--></code></div></li>
```