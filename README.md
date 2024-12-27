# Bitrix Product Library

### Simplify the process of modifying or creating Bitrix Elements and Products.

---

## 📦 Installation

1. Copy the library files into your Bitrix project at:  
   `/local/php_interface/include/{your_name_for_lib}`  
   (e.g., `/local/php_interface/include/bitrix_product-master`).

2. Update or create the `init.php` file in `/local/php_interface/`:

   ```php
   \Bitrix\Main\Loader::registerAutoloadClasses(
       null,
       [
           'Codeclass\\parser\\lib\\product\\BXElement' => '/local/php_interface/include/bitrix_product-master/BXElement.php',
           'Codeclass\\parser\\lib\\product\\BXProduct' => '/local/php_interface/include/bitrix_product-master/BXProduct.php',
           'Codeclass\\parser\\lib\\product\\Base' => '/local/php_interface/include/bitrix_product-master/Base.php',

           'Codeclass\\parser\\lib\\product\\props\\Enum' => '/local/php_interface/include/bitrix_product-master/props/Enum.php',
           'Codeclass\\parser\\lib\\product\\props\\EnumHL' => '/local/php_interface/include/bitrix_product-master/props/EnumHL.php',
           'Codeclass\\parser\\lib\\product\\props\\Image' => '/local/php_interface/include/bitrix_product-master/props/Image.php',
           'Codeclass\\parser\\lib\\product\\props\\Price' => '/local/php_interface/include/bitrix_product-master/props/Price.php',
           'Codeclass\\parser\\lib\\product\\props\\Product' => '/local/php_interface/include/bitrix_product-master/props/Product.php',
           'Codeclass\\parser\\lib\\product\\props\\Prop' => '/local/php_interface/include/bitrix_product-master/props/Prop.php',
           'Codeclass\\parser\\lib\\product\\props\\PropEList' => '/local/php_interface/include/bitrix_product-master/props/PropEList.php',
           'Codeclass\\parser\\lib\\product\\props\\PropFile' => '/local/php_interface/include/bitrix_product-master/props/PropFile.php',
           'Codeclass\\parser\\lib\\product\\props\\PropHTML' => '/local/php_interface/include/bitrix_product-master/props/PropHTML.php',
           'Codeclass\\parser\\lib\\product\\props\\PropInt' => '/local/php_interface/include/bitrix_product-master/props/PropInt.php',
           'Codeclass\\parser\\lib\\product\\props\\PropList' => '/local/php_interface/include/bitrix_product-master/props/PropList.php',
           'Codeclass\\parser\\lib\\product\\props\\PropListHL' => '/local/php_interface/include/bitrix_product-master/props/PropListHL.php',
           'Codeclass\\parser\\lib\\product\\props\\PropString' => '/local/php_interface/include/bitrix_product-master/props/PropString.php',
           'Codeclass\\parser\\lib\\product\\props\\Section' => '/local/php_interface/include/bitrix_product-master/props/Section.php',
       ]
   );
   ```

---

## 🚀 Example Usage

Here’s how you can use the library to create and modify Bitrix products:

```php
$prod = new BXProduct(IBLOCK_ID);

// Set basic fields
$prod->setField('NAME', 'Hello!');
$prod->setField('ACTIVE', 'Y');
$prod->setField('XML_ID', 'my_xml_id');

// Set properties
$prod->setProperty('STRING', 'String!!');
$prod->setSection('Section 1');

// Add images
$prod->setField('DETAIL_PICTURE', "/upload/img.png");
$prod->setField('PREVIEW_PICTURE', "/upload/img2.png");

// Add text content
$prod->setField('DETAIL_TEXT', 'Lorem Ipsum Dolor');
$prod->setField('DETAIL_TEXT_TYPE', 'html');

// Add list properties
$prod->setProperty('LIST2M', ['ListValue1', 'ListValue2']);

// Create an offer
$offer = $prod->addOffer();
$offer->setField('NAME', 'Offer1');
$offer->setField('XML_ID', 'myxmlid');
$offer->setProperty('ARTIKUL', 239239);
$offer->setPurchasingPrice(800);
$offer->setBasePrice(1000);
$offer->setQuantity(10);
$offer->setField('ACTIVE', 'Y');

// Add offer images
$offer->setField('DETAIL_PICTURE', "/upload/img1.png");
$offer->setField('PREVIEW_PICTURE', "/upload/img2.png");

// Save the product and its offers
$prod->save();
```

---

## 📝 Notes

- **Fields**: Correspond to options in the "Infoblock" (`инфоблок`) tab.
- **Properties**: Correspond to options in the "Properties" (`свойства`) tab.

---

## 💡 Features

- Streamlined creation and modification of Bitrix elements and products.
- Support for managing product fields, properties, sections, offers, and prices.
- Built-in methods for handling images and text.