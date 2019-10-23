Easy modify or create Bitrix Element or Bitrix Product        
```php
        $prod = new BXProduct(IBLOCK_ID);
        $prod->setField('NAME', 'Hello!');
        $prod->setField('ACTIVE', 'Y');
        $prod->setField('XML_ID', 'my_xml_id');


        $prod->setProperty('STRING', 'String!!');
        $prod->setSection('Section 1');

        $prod->setField('DETAIL_PICTURE', "/upload/img.png");
        $prod->setField('PREVIEW_PICTURE', "/upload/img2.png");

        $prod->setField('DETAIL_TEXT', 'Lorem Ipsum Dolor');
        $prod->setField('DETAIL_TEXT_TYPE', 'html');

        $prod->setProperty('LIST2M', ['ListValue1', 'ListValue2']);

        $offer = $prod->addOffer();
        $offer ->setField('NAME', 'Offer1');
        $offer ->setField('XML_ID', 'myxmlid');
        $offer->setProperty('ARTIKUL', 239239);
        $offer->setPurchasingPrice(800);
        $offer->setBasePrice(1000);
        $offer->setQuantity(10);
        $offer->setField('ACTIVE', 'Y');

        $offer->setField('DETAIL_PICTURE', "/upload/img1.png");
        $offer->setField('PREVIEW_PICTURE', "/upload/img2.png");
        $prod->save();
```