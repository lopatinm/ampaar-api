<?php
namespace app\commands;


use app\modules\v1\models\District;
use app\modules\v1\models\Locality;
use app\modules\v1\models\Nasleg;
use yii\console\Controller;

class LocalityController extends Controller
{

    public function actionInit()
    {
        $data = $this->parse_csv_file( '/home/lopatinm/naslega.csv' );
        $i = 0;
        foreach ($data as $feature){
            //print_r( $feature );
            $i++;
            $district = District::find()->where(array('name' => trim($feature[2])))->orderBy(['id' => SORT_DESC])->asArray()->One();

            $nasleg = Nasleg::find()->where(array('name' => trim($feature[1])))->andWhere(array('district_id' => $district['id']))->orderBy(['id' => SORT_DESC])->asArray()->One();
            echo $i."          ";

            $locality = new Locality;
            $locality->nasleg_id = $nasleg['id'];
            $locality->name = $feature[0];
            $locality->alias = $this->translit($feature[0]);
            $locality->latitude = $feature[3];
            $locality->longitude = $feature[4];
            $locality->save();

        }

        //print_r( $data );
    }

    public static function translit($text){
        $s = (string) $text;
        $s = strip_tags($s);
        $s = str_replace(array("\n", "\r"), " ", $s);
        $s = preg_replace("/\s+/", ' ', $s);
        $s = trim($s);
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sh','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
        $s = str_replace(" ", "-", $s);
        return $s;
    }

    function parse_csv_file( $file_path, $file_encodings = ['cp1251','UTF-8'], $col_delimiter = '', $row_delimiter = '' ){

        if( ! file_exists( $file_path ) ){
            return false;
        }

        $cont = trim( file_get_contents( $file_path ) );

        $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding( $cont, $file_encodings ) );

        unset( $cont );

        if( ! $row_delimiter ){
            $row_delimiter = "\r\n";
            if( false === strpos($encoded_cont, "\r\n") )
                $row_delimiter = "\n";
        }

        $lines = explode( $row_delimiter, trim($encoded_cont) );
        $lines = array_filter( $lines );
        $lines = array_map( 'trim', $lines );


        if( ! $col_delimiter ){
            $lines10 = array_slice( $lines, 0, 30 );


            foreach( $lines10 as $line ){
                if( ! strpos( $line, ',') ) $col_delimiter = ';';
                if( ! strpos( $line, ';') ) $col_delimiter = ',';

                if( $col_delimiter ) break;
            }

            if( ! $col_delimiter ){
                $delim_counts = array( ';'=>array(), ','=>array() );
                foreach( $lines10 as $line ){
                    $delim_counts[','][] = substr_count( $line, ',' );
                    $delim_counts[';'][] = substr_count( $line, ';' );
                }

                $delim_counts = array_map( 'array_filter', $delim_counts );

                $delim_counts = array_map( 'array_count_values', $delim_counts );

                $delim_counts = array_map( 'max', $delim_counts );

                if( $delim_counts[';'] === $delim_counts[','] )
                    return array('Не удалось определить разделитель колонок.');

                $col_delimiter = array_search( max($delim_counts), $delim_counts );
            }

        }

        $data = [];
        foreach( $lines as $key => $line ){
            $data[] = str_getcsv( $line, $col_delimiter );
            unset( $lines[$key] );
        }

        return $data;
    }
}