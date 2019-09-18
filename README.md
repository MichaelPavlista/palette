# Palette v 2.7.0
PHP rozšíření umožňující pokročilou manipulaci s obrázky, vytváření jejich miniatur a verzí.

## K čemu Palette slouží a jak funguje?
- Palette slouží k jednoduchému tvoření různých variant obrázku.
- Varianta daného obrázku je vždy generovaná při první požadavku na požadovanou variantu obrázku.
- Generování varianty obrázku se neprovádí při běhu spušťěného PHP scriptu, Palette pouze **vygeneruje url** adresu na které bude požadovaná varianta dostupná. Až při naštívění této url se tato varianta (pokud nebyla již vytvořena dříve) vygeneruje.   Díky tomuto principu vytváření variant obrázku není problém na jedné stránce generovat klidně 100+ variant bez toho, aby PHP došly prostředky, nebo načítání stránky trvalo delší dobu.

## Instalace a nastavení
#### 1. Palette naistalujeme do projektu nejlépe pomocí composeru.

     php composer.phar require pavlista/palette

#### 2. Vytvoříme instanci služby **Palette\Service**, která zajišťuje přístup k funkcím Palette.
Třída má pouze jeden povinný parametr a to instanci třídy, která implementuje interface Palette\Generator\IPictureGenerator v Palette je již připravená implementace a to třída **Palette\Generator\Server**.

**Argumenty Palette\Generator\Server jsou:**
- **storagePath**: Relativní nebo absolutní cesta ke složce do které se mají vygenerované miniatury a obrázky ukládat. Tato složka musí existovat a musí být do ní možné zapisovat!
- **storageUrl** Absolutní url adresa s lomítkem na konci na které je složka s miniatury veřejně dostupná.
- **basePath:** Absolutní cesta k document rootu webu. Tento parametr je nepovinný.

**Vytvoření instance Palette by tedy mělo vypadat takto:**

```php
$generator = new Palette\Generator\Server(

    'files/thumbs', // storagePath
    'http://www.example.com/files/thumbs/', // storageUrl
    '/var/www/example.com/', // basePath
    '%signingKey%'
);

$palette = new Palette\Service($generator);
```

#### 3. Vytvoříme a připravíme backend pro Palette
V umístění, které jsme si zvolily jako úložiště vygenerovaných variant (storagePath) je nutné vytvořit soubor palette-server.php (jméno může být libovolné), v kterém na instanci služby Palette (Palette\Service) zavoláme metodu serverResponse.

**Kód souboru by měl vypadat například takto:**
```php
<?php
// !!! Zde je nutné implementovat získání již nakonfigurované instance služby Palette.
// Popřípadě ji vytvořit se stejným nastavením znovu. V tom případě ale pozor na zadávané cesty.
$palette->serverResponse();
```

#### 4. Přesměrujeme neexistující soubory a adresáře na backend
V umístění, které jsme si zvolily jako úložiště nastavíme přesměrování všech neexistujících souborů a adresářů na vytvořený soubor s backendem.

##### Vzorové Nastavení v Apache
Do složky úložiště přidáme následující soubor .httacess:
```apache
#<IfModule mod_rewrite.c>
    RewriteEngine on
    #RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^.*$ palette-server.php [PT,L]
#</IfModule>
```

##### Vzorové Nastavení Nginx
Do sekce nastavení aktuálního serveru přidáme sekci:
```nginx
location /files/thumbs/ {

    try_files $uri $uri/ /files/thumbs/palette-server.php$is_args$args;
}
```

## Používání Palette
Varianty obrázku se tvoří ze zdrojového obrázku pomocí zápisu **image query**.
Pomocí image query se určuje které všechny effekty a transformace chceme na obrázek použít a v jakém pořadí to má být.

### Zápis Image Query
**1)** Image query začíná relativní nebo absolutní cestou k souboru zdrojového obrázku, po ní následuje znak `@`.
Tato část image query nemusí být zadána, pokud již máme nějakým způsobem nastavené z kterého obrázku chceme vytvářet variantu.

**Příklad:** `files/obrazek.png@`

**2)** Po té následuje výčet filtrů které chceme na obrázek aplikovat. Jednotlivé filtry se oddělují znakem `&` nebo `|`

**Příklad:** `files/obraze.png@Resize;100;200&Grayscale`

Přehled jednotlivých filtrů s popisem a příklady je k [nalezení zde.](http://palette.pavlista.cz/)

**3)** Některé filtry používají parametry, pomocí kterých se nastavují další vlastnosti filtru.
Tyto parametry oddělují pomocí středníku `;`.

**Příklad:** `files/obrazek.png@Resize;150&Grayscale&Contrast;-50`

**4)** U některých parametrů filrů které jsou nepovinné, nebo mají defaultní hodnotu lze jejich vyplnění v query přeskočit pomocí střeníku.

**Příklad:** `files/obrazek.png@Resize;150;;crop`

### Příklady reálného použití V PHP
**1)** Různé možnosti zápisu pro získání url adresy k obrázku zmenšeného na 150 x 120px:
```php
/**
 * @var $palette Palette\Service
 */
echo $palette->getUrl('image.png', 'Resize;150;120');
echo $palette->getUrl('image.png@Resize;150;120');
echo $palette('image.png', 'Resize;150;120');
echo $palette('image.png@Resize;150;120');
```
**2)** Zápis složitějšího příkazu v image query:
```php
/**
 * @var $palette Palette\Service
 */
echo $palette->getUrl('image.png', 'Resize;150;120&Rotate;-90&Border;1;1;#ccc');
```
**3)** Příklad zobrazení miniatury v základním PHP a HTML:
```html+php
<img src='<?=$palette('image.png@Resize;150;120')?>' alt='Resized image' />
```

### Důležité odkazy
- [Dokumentace filtrů Palette](http://palette.pavlista.cz/)
- [Rozšíření pro Nette](https://github.com/MichaelPavlista/nette-palette)