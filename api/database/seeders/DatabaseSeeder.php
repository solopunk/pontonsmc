<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BoatType;
use App\Models\Homepage;
use App\Models\Homeport;
use App\Models\MailType;
use App\Models\MemberType;
use App\Models\Tab;
use App\Models\User;
use Durlecode\EJSParser\HtmlParser;
use Durlecode\EJSParser\Parser;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // super
        Admin::create([
            'email' => 'webmaster@media-events.mc',
            'password' => Hash::make('Mediadmin98!')
        ]);

        // member types
        foreach ([
            [
                'name' => 'Sympathisant',
                'uid' => 'supporter'
            ],
            [
                'name' => 'Actif',
                'uid' => 'active'
            ],
            [
                'name' => 'Comité Directeur',
                'uid' => 'committee'
            ],
            [
                'name' => 'Retardataire',
                'uid' => 'latecomer'
            ],
        ] as $type) {
            MemberType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        // boat types
        foreach ([
            [
                'name' => 'Moteur',
                'uid' => 'engine'
            ],
            [
                'name' => 'Voile',
                'uid' => 'sail'
            ],
        ] as $type) {
            BoatType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        // homeports
        foreach ([
            [
                'name' => 'Hercule',
                'uid' => 'hercule'
            ],
            [
                'name' => 'Fontvieille',
                'uid' => 'fontvieille'
            ],
            [
                'name' => 'Autre',
                'uid' => 'other'
            ],
        ] as $type) {
            Homeport::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        foreach ([
            [
                'name' => 'Nouvelle',
                'uid' => 'new'
            ],
            [
                'name' => 'Relance',
                'uid' => 'reminder'
            ],
        ] as $type) {
            MailType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        };

        // tabs
        foreach ([
            [
                'title' => 'Objet',
                'content' => "L'association a pour objet la représentation, la promotion et le maintien de la petite et moyenne plaisance en Principauté de Monaco, en accord avec les usages et traditions. L'organisation de toute manifestation ou activité ayant pour vocation de regrouper les propriétaires de bateaux ayant une place dans les Ports de Monaco, dont l'embarcation ne dépasse pas 18 mètres. En outre, l'assocation, a pour vocation de devenir un interlocuteur privilégié auprès des autorités compétentes, dans un esprit de dialogue et de concertation. L'Association souhaite également jouer un rôle dans la lutte contre les bateaux épaves."
            ],
            [
                'title' => 'Membres',
                'content' => "Conformément aux statuts, l'association est constitutée de membres répartis en 3 catégories : Membre actif : Tout propriétaire d'un bateau sous pavillon monégasque dont la taille ne dépasse pas les 18 mètres. Le membre actif doit impérativement être attributaire ou demandeur d'une place dans les Ports de Monaco. En outre, il doit résider en Principauté de Monaco. Membre sympathisant : Toute personne physique ou morale souhaitant soutenir l'action de l'association, mais n'ayant pas un bateau remplissant les conditions d'admission au titre de membre actif. Membre d'honneur : Toute personne physique dont le comportement remarquable en faveur de la petite et moyenne plaisance en Principauté aura été reconnu par le Comité Directeur. Voir les tarifs des cotisations."
            ],
            [
                'title' => 'Cotisations',
                'content' => "Chaque cotisation permet à l'association de couvrir ses frais de fonctionnement. Les cotisations permettront également à l'association de pouvoir organiser des événements, notamment le fameux \"pastu des pontons\" en début de saison. D'avance, merci à tous de votre soutien ! • Membre actif : 40 € / an • Membre sympathisant : minimum 50 € / an"
            ],
            [
                'title' => 'Sections',
                'content' => "Notre association se veut la plus ouverte possible. Nous souhaitons que ses activités correspondent aux attentes et aux préoccupations de la petite et moyenne plaisance en Principauté de Monaco. A ce jour, nous envisageons de créer les sections suivantes : Voiliers Bateaux moteurs inférieurs à 8 mètres Bateaux moteurs supérieurs à 8 mètres Bateaux de tradition Sorties en mer Activités sportives Pêche en mer Confort des ports En complément de ces sections, nous désignerons un responsable par ponton. Si vous êtes intéressés, n'hésitez pas à nous contacter."
            ],
            [
                'title' => 'Comité Directeur',
                'content' => "Membres fondateurs : - Gérard Aubert, - Jean-François Carpinelli, - Jean-Claude Degiovanni, - Arnaud Giusti, - Franck Lobono. Bureau : - Franck Lobono, Président. - Jean-Claude Degiovanni, Vice-Président, - Arnaud Giusti, Secrétaire Général, - Alain Bermond, Trésorier. Membres du Comité Directeur : - Jean-François Blanchi - Stéphane Lorenzi - Jean Fontaine"
            ],
            [
                'title' => 'Charte du plaisancier',
                'content' => "L'adhésion à l'Association des Pontons de Monaco est un engagement responsable. Chaque membre doit respecter les points suivants : Entretenir son embarcation et l'ensemble de ses accessoires, notamment sur les plans mécaniques et esthétiques. Le bateau doit toujours être propre et en état de marche. Garantir que l'usage de son bateau soit conforme à la pratique de la plaisance à titre personnel. Respecter son voisinage, notamment en s'assurant que le bateau soit correctement équipé de défenses propres et adaptées à la taille de l'embarcation. S'assurer d'un amarrage effectué dans les règles de l'art, notamment en veillant à ce que les amarres arrières et avant soient correctement tendues, postionnant le bateau dans l'axe de sa place. Veiller, par jour de vent, à ce que le bateau ne cause pas de dégâts aux embarcations voisines. Manœuvrer lors des sorties et des retours dans sa place avec prudence et dans le plus grand respect des embarcations voisines, positionnant systématiquement des défenses. Signaler à la capitainerie toute absence lors d'un séjour prolongé en dehors du port. Laver son bateau en veillant à limiter sa consomation d'eau et l'usage de détergents nocifs pour l'environnement. Porter assistance à tout plaisancier en difficulté dans les Ports de Monaco. Signaler aux autorités porturaires tout problème ou situation anormalement constatés."
            ],
            [
                'title' => 'Contact',
                'content' => "Siège social : 9, avenue des Castelans - Entrée F 98000 MONACO contact@pontonsdemonaco.com"
            ],
            [
                'title' => 'Modalités d\'adhésion',
                'content' => "Conformément aux status de l'association, l'adhésion est soumise à la validation du Comité Directeur. Après accord, le futur membre est informé par courrier ou par email. L'adhésion à l'association n'est définitive qu'après que le demandeur ait transmis à l'association les documents suivants : copie du congé du bateau, copie de la dernière facture SEPM, chèque correspondant au montant de la cotisation annuelle (chèque à l'ordre de \"pontons de Monaco\"). Voir les tarifs Toute demande d'adhésion est condtionnée par l'acceptation de la Charte du Plaisancier édictée par les Pontons de Monaco."
            ],
        ] as $tab) {

            $parser = new HtmlParser(
                "<p class=\"prs-paragraph\">{$tab['content']}</p>"
            );

            $json = $parser->toBlocks();
            $html = Parser::parse($json)->toHtml();

            Tab::create([
                'title' => $tab['title'],
                'content_json' => $json,
                'content_html' => $html
            ]);
        };

        // homepage
        $homepageIntro = 'Cher plaisanciers, Bienvenue sur le site officiel de l\'association des «Pontons de Monaco». Nous nous sommes regroupés par passion pour la mer et par amour de nos ports, Hercule et Fontvieille. Nous aspirons au maintien d\'une plaisance locale dynamique, dans le respect des traditions instaurées par nos Princes. Propriétaires de bateaux de 0 à 18m sous pavillon monégasque, dans un esprit solidaire comme le veut la tradition maritime, dans une démarche constructive, comme l\'impose notre attachement à Monaco, nous vous proposons de rejoindre notre association.';

        $parser = new HtmlParser(
            "<p class=\"prs-paragraph\">{$homepageIntro}</p>"
        );

        $introJson = $parser->toBlocks();
        $introHtml = Parser::parse($json)->toHtml();

        Homepage::create([
            'title' => 'L\'association de promotion et de maintien  de la petite et moyenne plaisance dans les ports de Monaco',
            'intro_json' => $introJson,
            'intro_html' => $introHtml,
        ]);

        $this->call(DataTestSeeder::class);
    }
}
