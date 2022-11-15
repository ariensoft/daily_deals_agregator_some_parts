<?php

header("Content-type: text/xml");

mysql_connect("localhost", "root", "passwd here") or die("Nelze se připojit k MySQL: " . mysql_error());
mysql_select_db("import") or die("Nelze vybrat databázi: " . mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION='utf8_czech_ci'");

function parseToXML($htmlStr) {
    $xmlStr = str_replace('<', '&lt;', $htmlStr);
    $xmlStr = str_replace('>', '&gt;', $xmlStr);
    $xmlStr = str_replace('"', '&quot;', $xmlStr);
    $xmlStr = str_replace("'", '&#39;', $xmlStr);
    $xmlStr = str_replace("&", '&amp;', $xmlStr);
    return $xmlStr;
}

$Sql = mysql_query("SELECT Deals.*, Servers.Name as prodejce, SubCategories.SubName as subcat FROM Deals left join Servers on Deals.ServerId = Servers.ServerId left join SubCategories on Deals.SubCatId = SubCategories.SubCatId WHERE Deals.CityId IN (6252) AND Deals.CategoryId = 3 AND Deals.Status = 2 group by Deals.Text ORDER BY `Deals`.`ServerId` ASC");
$pocet = mysql_num_rows($Sql);
if ($pocet > 0) {
    echo '<MS>';
    while ($data = mysql_fetch_array($Sql)):
        
        $subcat = $data["subcat"];
        $dealUrl = urlencode($data["Url"]);
        
        echo '<DEAL>';
        echo "<ID>" . parseToXML($data["DealId"]) . "</ID>";
        echo "<SERVER>" . parseToXML($data["ServerId"]) . "</SERVER>";
        echo "<TITLE>" . parseToXML($data["Text"]) . "</TITLE>";
        echo "<TITLE_FULL>" . parseToXML($data["TextFull"]) . "</TITLE_FULL>";
        echo "<TITLE_SEARCH>" . parseToXML($data["SearchText"]) . "</TITLE_SEARCH>";
        echo "<URL>" . $dealUrl . "</URL>";
        echo "<IMAGE_URL>" . parseToXML($data["Image"]) . "</IMAGE_URL>";
        echo "<FINAL_PRICE>" . parseToXML($data["FPrice"]) . "</FINAL_PRICE>";
        echo "<ORIGINAL_PRICE>" . parseToXML($data["OPrice"]) . "</ORIGINAL_PRICE>";
        echo "<DISCOUNT>" . parseToXML($data["Discount"]) . "</DISCOUNT>";
        echo "<SAVINGS>" . parseToXML($data["OPrice"] - $data["FPrice"]) . "</SAVINGS>";
        echo "<DEAL_START>" . parseToXML($data["DStart"]) . "</DEAL_START>";
        echo "<DEAL_END>" . parseToXML($data["DEnd"]) . "</DEAL_END>";
        echo "<CUSTOMERS>" . parseToXML($data["Customers"]) . "</CUSTOMERS>";
        echo "<CATEGORY>" . parseToXML($subcat) . "</CATEGORY>";
        echo "<CATID>" . $data["CategoryId"] . "</CATID>";
        echo '</DEAL>';
        unset($tagy);
    endwhile;
    echo '</MS>';
}
?>
