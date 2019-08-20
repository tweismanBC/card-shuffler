<?php
namespace App\Command;

use App\Entity\Card;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Psr\Container\ContainerInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DealCardsCommand extends Command
{
    protected $filesystem;
    protected $serializer;

    private $hand;

    public function __construct()
    {
        // instantiate the symfony core files will need for this application: (normally this can be done via dependency injection in a full blown application)
        $this->filesystem = new Filesystem();

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);

        // main "hand" of cards array:
        $this->hand = array();
        parent::__construct();
    }

    protected function configure()
    {
        // the short description shown while running "php bin/console list"
        $this->setDescription('Parses a deck of cards under ./assets/cardsDeck.json, shuffles, and deals out a hand of five cards at random.');

        // to run manually: php console.php card-shuffler:shuffle-and-deal
        $this->setName('card-shuffler:shuffle-and-deal');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->filesystem->exists("./assets/cardsDeck.json")) {
            $output->writeln("===== Welcome to the Pipboy card shuffler 3000! =====");

            // initial user prompt:
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>System: Would you like us to deal you a hand (y/n):</question> ', false);
            $gamePlayQuestion = new ConfirmationQuestion('<question>Would you like to draw another hand (y/n):</question>', false);

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln("System: I guess I didn't want to play either... I'll be going now.\r\n");
                return;
            } else {
                $output->writeln("System: Yay! I'll go ahead and shuffle the cards for you.\r\n");

                /**
                 * serialize the contents of cardsDeck.json into the corresponding Entities, this could be done easier with a full set-up of JMSSerializer or another bundle that would auto serialize all card "objects" into
                 * an ArrayCollection of card entities. This is far less elegant however JMS requires yaml configuration which is out of the scope of a command line tool
                 */
                $deck = array();
                $cardsDeckJson = json_decode(file_get_contents('./assets/cardsDeck.json'), true);
                foreach ($cardsDeckJson as $cardArray) {
                    $card = $this->serializer->deserialize(json_encode($cardArray), "App\\Entity\\Card", 'json');
                    array_push($deck, $card);
                }

                $output->writeln("System: I'm shuffling the cards now, do you prefer a riffle shuffle or 52 card pick up? Because I can't do either...\r\n");
                //print_r($deck);
                shuffle($deck);

                // primary game loop: draw cards off the top of the deck until there are no cards remaining or the user wants to stop playing
                do {
                    $deck = $this->createHand($deck);
                    $this->displayHand($output);
                    if (count($deck) > 0)
                        $output->writeln("Total Cards Remaining in Deck: ".count($deck));
                    else
                        break;
                } while ($helper->ask($input, $output, $gamePlayQuestion));

                // Exit code/statement:
                $output->writeln("System: thanks for playing have a great day!");
                $output->writeln("Disclaimer this product is not actually \"officially\" affiliated with vault tech or is subsidiaries in any way.");
            }

        } else {
            // initial check failed, this will only run if there is a cardsDeck.json file in the assets folder
            $output->writeln("<error>Warning: the cardsDeck.json file was not found under ./assets/ directory. Please double check your deck of cards actually exists before trying to play this game.\r\n</error>");
        }

        return true;
    }

    private function createHand($deck): array {
        $this->hand = array();
        for($i = 0; $i < 5; $i++) {
            if (count($deck) != 0)
                array_push($this->hand, array_pop($deck));
            else
                break;
        }

        return $deck;
    }

    private function displayHand($output) {
        $output->writeln("===== Your Current Hand =====");
        foreach ($this->hand as $card) {
            $output->writeln("The ".$card->getCard()." of ".$card->getSuit());
        }

        $output->writeln("");
    }
}
