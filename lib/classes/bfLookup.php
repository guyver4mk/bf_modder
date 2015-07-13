<?php
require_once(dirname(__FILE__).'/../../includes/configuration.php');

class bfLookup
{
	private $db;
	private $file;
	private $sph = 'no_sel';
	public function __construct()
	{
		$this->db = new DB(DBHOST, DBUSER, DBPASS, DBNAME);

	}

	public function setFile($file)
	{
		$this->file = $file;
	}

	public function getString($key,$val)
	{
		$ret = "";
		if(strpos($key, '@') !== false)
		{
			$user = $key;
		}
		elseif($key=='sphere'&& $key!='no_sel')
		{
			$hex = $this->setSphereHex($val);
			$this->sph = $hex;
			$ret .= 'printf "'.$hex.'" | dd of=/var/www/html/shells/'.$this->file.'.so ibs=14 obs=1 seek=$((0x172736)) count=1 conv=notrunc'."\n";
			$ret .= 'printf "\x01\x20\x10\xBD" | dd of=/var/www/html/shells/'.$this->file.'.so ibs=4 obs=1 seek=$((0x17271A)) count=1 conv=notrunc'."\n";
		}	
		else
		{
			if(($this->sph=='no_sel')&&(isset($key['itemG'])&& $key['itemG']=='on'))
			{
				unset($key['itemG']);
				$ret .= 'printf "\x01\x20\x10\xBD" | dd of=/var/www/html/shells/'.$this->file.'.so ibs=4 obs=1 seek=$((0x17271A)) count=1 conv=notrunc'."\n";
			}

			$keyVals = $this->lookupValues($key);		
		}
		

		foreach($keyVals as $row)
		{
			$ret .= 'printf "'.$row['mod_value'].'" | dd of=/var/www/html/shells/'.$this->file.'.so ibs='.$row['mod_size'].' obs=1 seek=$(('.$row['mod_offset'].')) count=1 conv=notrunc'."\n";
		}

		return $ret;

	}

	private function lookupValues($key)
	{

		$sql = "SELECT * FROM mods
				WHERE `mod` = :mod";

		$params = array(
					array(
						'name'	=> ':mod',
						'value'	=> $key,
						'type'	=> 'string'
				));

		$ret = $this->db->query($sql,$params);



		return $ret;
	}


	public function getVersion()
	{
		$sql = "SELECT * FROM info
				WHERE `version_name` = 'current'";


		$ret = $this->db->query($sql);

		return $ret[0];	
	}


	public function getMods($version)
	{
		$sql = "SELECT * FROM mods2
				WHERE active = 1
				AND mod_version=:version";

		$params = array(
				array(
					'name' => ':version',
					'value' => $version,
					'type' => 'string'
				)
			);
	
		$res = $this->db->query($sql, $params);


		$ret = "";

		foreach($res as $row)
		{
			if($row['input_type'] == 'checkbox')
			{
				$ret .= '<div class="col-lg-offset-2 col-lg-10 height:30px">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="'.$row['mod'].'"> '.$row['mod_nice_name'].'
                            </label>
                          </div>
                        </div>'."\n";
			}
			else
			{
				$ret .="";
			}
		}

		return $ret;
	}


































	public function sphereLookup()
	{
		$ret = "<select name=\"sphere\" id=\"sphereList\" class=\"selectpicker\">
		<option value=\"no_sel\">-Select to Enable-</option>
	 		<option value=\"818901\"> Frozen Myth</option>
		<option value=\"30000\"> Famous Blade</option>
		<option value=\"30001\"> Holy Blade</option>
		<option value=\"30002\"> Beast Blade</option>
		<option value=\"30100\"> Giant Shield</option>
		<option value=\"30101\"> Dragon Seal</option>
		<option value=\"30102\"> Goddess Seal</option>
		<option value=\"30200\"> White Cane</option>
		<option value=\"30201\"> Sentry Cane</option>
		<option value=\"30202\"> Holy Cane</option>
		<option value=\"30300\"> Spirit Armor</option>
		<option value=\"30301\"> Beast Armor</option>
		<option value=\"30302\"> Holy Robe</option>
		<option value=\"30400\"> Champion Axe</option>
		<option value=\"30401\"> Magical Axe</option>
		<option value=\"31000\"> Muramasa</option>
		<option value=\"31100\"> Royal Shield</option>
		<option value=\"31200\"> Death Scythe</option>
		<option value=\"31300\"> Gilded Pearl</option>
		<option value=\"31500\"> Death Axe</option>
		<option value=\"31600\"> Poison Shiv</option>
		<option value=\"31700\"> Demon Rifle</option>
		<option value=\"31800\"> Vine Lance</option>
		<option value=\"31900\"> Beast Spear</option>
		<option value=\"32000\"> Cursed Sabre</option>
		<option value=\"32100\"> Thunder Bow</option>
		<option value=\"32200\"> Monster Robe</option>
		<option value=\"32201\"> Prized Cloth</option>
		<option value=\"32300\"> Poison Light</option>
		<option value=\"32400\"> Faint Light</option>
		<option value=\"32500\"> Growth Light</option>
		<option value=\"32600\"> Broken Light</option>
		<option value=\"32700\"> Cursed Light</option>
		<option value=\"32800\"> Numb Light</option>
		<option value=\"32900\"> Star Blade</option>
		<option value=\"33000\"> Divine Crest</option>
		<option value=\"33001\"> Royal Crest</option>
		<option value=\"33400\"> Ares' Crest</option>
		<option value=\"33500\"> Cursed Robe</option>
		<option value=\"33501\"> Demon Robe</option>
		<option value=\"33600\"> Soul Spear</option>
		<option value=\"33700\"> Skill Bracer</option>
		<option value=\"33800\"> Cure Bracer</option>
		<option value=\"33900\"> Zelnite Ring</option>
		<option value=\"34000\"> Soul Bracer</option>
		<option value=\"34100\"> Thief Bracer</option>
		<option value=\"34300\"> Light Helm</option>
		<option value=\"34301\"> Star Helm</option>
		<option value=\"34400\"> Clear Cloak</option>
		<option value=\"34500\"> Wise Mask</option>
		<option value=\"34600\"> Stealth Robe</option>
		<option value=\"34700\"> Holy Crown</option>
		<option value=\"34800\"> Glass Crown</option>
		<option value=\"34803\"> Tech Gizmo 2</option>
		<option value=\"34804\"> Cure Gizmo 2</option>
		<option value=\"34805\"> Royal Bud</option>
		<option value=\"34806\"> Dragon Ring</option>
		<option value=\"34807\"> Demon Lance</option>
		<option value=\"34808\"> Phoenix Eye (Town)</option>
		<option value=\"36600\"> Phoenix Eye (Frontier Hunter)</option>
		<option value=\"34809\"> Limbo Stone</option>
		<option value=\"34810\"> Death Sword</option>
		<option value=\"34811\"> Chaos Sword</option>
		<option value=\"34812\"> Spark Geyser</option>
		<option value=\"34813\"> Over Killer</option>
		<option value=\"34814\"> Multi Slayer</option>
		<option value=\"34815\"> Burst King</option>
		<option value=\"34816\"> Speed Star</option>
		<option value=\"34817\"> Challenger</option>
		<option value=\"34818\"> Ally Savior</option>
		<option value=\"34900\"> Divine Spear</option>
		<option value=\"35000\"> Hero Stone</option>
		<option value=\"35001\"> Giant Stone</option>
		<option value=\"35002\"> Divine Stone</option>
		<option value=\"35100\"> Refined Gem</option>
		<option value=\"35200\"> Mech Sword</option>
		<option value=\"35201\"> Flesh Armor</option>
		<option value=\"35202\"> Sacred Jewel</option>
		<option value=\"35300\"> Crown Light</option>
		<option value=\"35301\"> Royal Light</option>
		<option value=\"35400\"> Cordelicite</option>
		<option value=\"35500\"> Alter Blade</option>
		<option value=\"35501\"> Dragon Sword</option>
		<option value=\"35600\"> Vampire Spear</option>
		<option value=\"35700\"> Drain Spear</option>
		<option value=\"35800\"> Medulla Gem</option>
		<option value=\"35900\"> Thief Gloves</option>
		<option value=\"36000\"> Omni Gizmo</option>
		<option value=\"36100\"> Blessed Robe</option>
		<option value=\"36200\"> Wicked Blade</option>
		<option value=\"36300\"> Evil Shard</option>
		<option value=\"36400\"> Steeple Rose</option>
		<option value=\"36401\"> Heavenly Bud</option>
		<option value=\"36500\"> King's Crown</option>
		<option value=\"819990\"> Burny's Soul Stone</option>
		<option value=\"819991\"> Creator's Blade</option>
		<option value=\"849990\"> Charge Stone</option>
		<option value=\"30003\"> Dragon Blade</option>
		<option value=\"30004\"> Divine Sword</option>
		<option value=\"30005\"> God Sword</option>
		<option value=\"30006\"> Chosen Blade</option>
		<option value=\"30007\"> Carnage Edge</option>
		<option value=\"30103\"> Star Shield</option>
		<option value=\"30104\"> King Shield</option>
		<option value=\"30105\"> Dogma Shield</option>
		<option value=\"30106\"> Grand Shield</option>
		<option value=\"30107\"> Ruler Shield</option>
		<option value=\"30203\"> Sky Staff</option>
		<option value=\"30204\"> Godly Staff</option>
		<option value=\"30205\"> Order Staff</option>
		<option value=\"30206\"> Worship Cane</option>
		<option value=\"30207\"> Nature Staff</option>
		<option value=\"30303\"> Shiny Armor</option>
		<option value=\"30304\"> God Armor</option>
		<option value=\"30305\"> Ember Armor</option>
		<option value=\"30306\"> Dark Armor</option>
		<option value=\"30307\"> Odd Armor</option>
		<option value=\"30402\"> Havoc Axe</option>
		<option value=\"31001\"> Angelic Foil</option>
		<option value=\"31002\"> Amanohabaken</option>
		<option value=\"31101\"> Exyl Shield</option>
		<option value=\"31102\"> Evil Shield</option>
		<option value=\"31201\"> Dragon Sword</option>
		<option value=\"31301\"> Hope Stone</option>
		<option value=\"31400\"> Zombie Jabot</option>
		<option value=\"31401\"> Fairy Choker</option>
		<option value=\"31601\"> Venom Knives</option>
		<option value=\"31701\"> Death Cannon</option>
		<option value=\"31801\"> Phantom Pike</option>
		<option value=\"31901\"> Ogre Lance</option>
		<option value=\"32001\"> Cursed Sword</option>
		<option value=\"32101\"> Zeus's Bow</option>
		<option value=\"32202\"> Divine Robe</option>
		<option value=\"32301\"> Venom Shard</option>
		<option value=\"32401\"> Weak Shard</option>
		<option value=\"32501\"> Ill Shard</option>
		<option value=\"32601\"> Weary Shard</option>
		<option value=\"32701\"> Curse Shard</option>
		<option value=\"32801\"> Stupor Shard</option>
		<option value=\"33002\"> Holy Emblem</option>
		<option value=\"33502\"> Zeus's Robe</option>
		<option value=\"33601\"> XXX Soul Spear</option>
		<option value=\"33602\"> Death Spear</option>
		<option value=\"33901\"> Zel Ring</option>
		<option value=\"34001\"> Beast Bracer</option>
		<option value=\"34302\"> Solar Helm</option>
		<option value=\"34701\"> King's crown_test</option>
		<option value=\"34702\"> Divine Crown</option>
		<option value=\"34801\"> Ruin Helm</option>
		<option value=\"34802\"> Dark Helm</option>
		<option value=\"36600\"> Phoenix Eye</option>
		<option value=\"36700\"> Heaven Sword</option>
		<option value=\"36701\"> Abyss Sword</option>
		<option value=\"36800\"> Dragon Stud</option>
		<option value=\"36900\"> Alzeon Pearl</option>
		<option value=\"37000\"> Geldnite Axe</option>
		<option value=\"37100\"> Masamune</option>
		<option value=\"37200\"> Cosmic Dust</option>
		<option value=\"37300\"> Magic Ore</option>
		<option value=\"37400\"> Legwand Gem</option>
		<option value=\"37500\"> Ragna Blade</option>
		<option value=\"37600\"> Dandelga</option>
		<option value=\"37610\"> Drevas</option>
		<option value=\"37620\"> Batootha</option>
		<option value=\"37630\"> Lexida</option>
		<option value=\"37640\"> Black Lance</option>
		<option value=\"37650\"> Urias</option>
		<option value=\"37700\"> Health Charm</option>
		<option value=\"37800\"> Lizeria Gem</option>
		<option value=\"37900\"> Divine Gem</option>
		<option value=\"38000\"> Demon Shard</option>
		<option value=\"38100\"> Bright Shard</option>
		<option value=\"38200\"> Royal Shard</option>
		<option value=\"38300\"> Thief Guards</option>
		<option value=\"38400\"> Bahamut Rage</option>
		<option value=\"38401\"> Blue Dragon</option>
		<option value=\"38402\"> Angel Sword</option>
		<option value=\"38403\"> Seal Sword</option>
		<option value=\"38500\"> Tree Shield</option>
		<option value=\"38501\"> Life Shield</option>
		<option value=\"38502\"> Alpha Shield</option>
		<option value=\"38503\"> Demon Shield</option>
		<option value=\"38600\"> Death Jewel</option>
		<option value=\"38601\"> Malice Jewel</option>
		<option value=\"38700\"> Divine Jewel</option>
		<option value=\"38800\"> Old Dagger</option>
		<option value=\"38900\"> Soul Gun</option>
		<option value=\"38801\"> World Lance</option>
		<option value=\"38901\"> Dark Lance</option>
		<option value=\"39000\"> Evil Halberd</option>
		<option value=\"39001\"> Demon Bow</option>
		<option value=\"39100\"> Demon Dress</option>
		<option value=\"39200\"> White Bangle</option>
		<option value=\"39201\"> Gold Bangle</option>
		<option value=\"39300\"> Miroku Pearl</option>
		<option value=\"39400\"> Godly Statue</option>
		<option value=\"39500\"> Summoner Hat</option>
		<option value=\"39600\"> Goddess Flag</option>
		<option value=\"39700\"> Demon Flag</option>
		<option value=\"39800\"> Demon Crown</option>
		<option value=\"39900\"> Phoenix Wing</option>
		<option value=\"40000\"> Sol Creator</option>
		<option value=\"40100\"> Luna Creator</option>
		<option value=\"40200\"> Fake Stone</option>
		<option value=\"40300\"> Flag Flower</option>
		<option value=\"40400\"> Sacred Gem</option>
		<option value=\"40500\"> Thief Cloak</option>
		<option value=\"40600\"> Luna Laguliz</option>
		<option value=\"40700\"> Divine Blade</option>
		<option value=\"40800\"> Flower Stud</option>
		<option value=\"40900\"> Elder Hat</option>
		<option value=\"41000\"> Shiny Anklet</option>
		<option value=\"41100\"> Dragon Studs</option>
		<option value=\"41200\"> Twinkle Gem</option>
		<option value=\"41300\"> Lament Blade</option>
		<option value=\"41400\"> Fire Mail</option>
		<option value=\"41401\"> Water Mail</option>
		<option value=\"41402\"> Earth Mail</option>
		<option value=\"41403\"> Thunder Mail</option>
		<option value=\"41404\"> Light Mail</option>
		<option value=\"41405\"> Dark Mail</option>
		<option value=\"41500\"> Zombie Jabot</option>
		<option value=\"41600\"> Starry Blade</option>
		<option value=\"41700\"> Wyvern Studs</option>
		<option value=\"41800\"> Pure Crystal</option>
		<option value=\"41900\"> Comet Helmet</option>
		<option value=\"42000\"> Cured Glass</option>
		<option value=\"42100\"> True Ore</option>
		<option value=\"42200\"> Astral Robe</option>
		<option value=\"42300\"> Buffer Jewel</option>
		<option value=\"42400\"> Virtue Stone</option>
		<option value=\"42500\"> Dragon Edge</option>
		<option value=\"42600\"> Summoner Key</option>
		<option value=\"42700\"> War Crown</option>
		<option value=\"42800\"> Soul Blade</option>
		<option value=\"42900\"> Star Flower</option>
		<option value=\"43000\"> Lens Shield</option>
		<option value=\"43100\"> God Lance</option>
		<option value=\"43200\"> Demon Core</option>
		<option value=\"819992\"> Royal Aegis</option>
		<option value=\"819993\"> Silver Aegis</option>
		<option value=\"819994\"> Blood Scythe</option>
		<option value=\"819995\"> Fiend Fang</option>
		<option value=\"819996\"> Song Jewel</option>
		<option value=\"819997\"> Force Amulet</option>
		<option value=\"819910\"> Brave Crest</option>
		<option value=\"819901\"> Fire Blade</option>
		<option value=\"819902\"> Inferno Blade</option>
		<option value=\"819903\"> Fire Shield</option>
		<option value=\"819904\"> Inferno Shield</option>
		<option value=\"819905\"> Water Shield</option>
		<option value=\"819906\"> Tidal Shield</option>
		<option value=\"849901\"> Electric Blade</option>
		<option value=\"849902\"> Lightning Blade</option>
		<option value=\"849903\"> Electric Shield</option>
		<option value=\"849904\"> Lightning Shield</option>
		<option value=\"849905\"> Earth Shield</option>
		<option value=\"849906\"> Terra Shield</option>
		<option value=\"819923\"> Brave Emblem</option>
		<option value=\"818883\"> Mystic Lantern</option>
		<option value=\"818880\"> Shadow Cloak</option>
		<option value=\"818881\"> Guardian Cloak</option>
		<option value=\"818882\"> Aegis Cloak</option>
		<option value=\"818884\"> Providence Ring</option>
		<option value=\"818890\"> Hallowed Skull</option>
		<option value=\"818891\"> Sinister Orb </option>
		<option value=\"818892\"> Ihsir's Guise</option>
		</select>";

		return $ret;
	}

	public function setSphereHex($id)
	{
		$sphere = $this->zeropad(dechex($id),8);

		return $sphere;
	}

	private function zeropad($num, $lim)
	{
		$len = strlen($num);
		$ret = $num;
		while($len < $lim)
		{
			$ret = '0'.$ret;
			$len++;
		}

		$num = str_split($ret, 2);
		$cnt = count($num);
		//$cnt = $cnt-1;
		$ret = $num[$cnt];
		while($cnt>=0)
		{
			$cnt--;
			$ret .= $num[$cnt];
		}

		$ret = str_split(strtoupper($ret), 2);

		$return = '\x02\x49\xC3\xF3\x10\xED\x28\x1C\x3E\xBD\x'.$ret[0]. '\x' . $ret[1] . '\x' . $ret[2] . '\x' . $ret[3];
	

	   return  $return;
	}

	public function checkProcessing()
	{
		$sql = "SELECT count(*) as processing  FROM apk_cron WHERE status ='processing'";
		$pro = $this->db->query($sql);

		return $pro[0]['processing'];
	}

	public function processApk($status, $file)
	{
		$sql = "SELECT * FROM apk_cron WHERE status NOT IN('done', 'processing') ORDER BY id asc LIMIT 1";

		$r = $this->db->query($sql);

		$row = $r[0];

		$sql = "UPDATE apk_cron SET status='processing' WHERE id = ".$row['id'];
		$this->db->apply($sql);

		shell_exec("sh /var/www/html/create_apk.sh ".$row['file_name']." " . $row['user_name']);


		$sql = "INSERT INTO apk_done(apk_link, cron_id, file_name, user_name) VALUES('".$row['user_name'].".gumi.bravefrontier_1.2.4.2_11014020.apk', '".$_SESSION['apk_id']."', '".$row['file_name']."', '".$row['user_name']."')";
		$this->db->apply($sql);

		$sql = "UPDATE apk_cron SET status='done' WHERE id = ".$row['id'];
		$this->db->apply($sql);

		$sql = "SELECT * FROM apk_done WHERE cron_id = ".$_SESSION['apk_id'];
		$d = $this->db->query($sql);

		$done = $d[0];
		$link_path = 'http://www.guyver4.co.uk/apks/'.$done['apk_link'];

		 $a = array(
		 	'apk_id' => $done['cron_id'],
		 	'apk_status' => 'done',
		 	'apk_name' => $done['file_name'],
		 	'apk_outputted' => $link_path
		 );

		 return $a;

	}


}
