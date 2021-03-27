<?php

namespace aieuo\mfconverter;

use aieuo\mfconverter\converter\MineflowToPHP;
use aieuo\mineflow\flowItem\action\inventory\AddItem;
use aieuo\mineflow\flowItem\action\inventory\EquipArmor;
use aieuo\mineflow\flowItem\action\item\AddEnchantment;
use aieuo\mineflow\flowItem\action\item\CreateItemVariable;
use aieuo\mineflow\flowItem\action\item\SetItemLore;
use aieuo\mineflow\flowItem\action\math\Calculate;
use aieuo\mineflow\flowItem\action\math\Calculate2;
use aieuo\mineflow\flowItem\action\math\FourArithmeticOperations;
use aieuo\mineflow\flowItem\action\math\GenerateRandomNumber;
use aieuo\mineflow\flowItem\action\player\GetPlayerByName;
use aieuo\mineflow\flowItem\action\player\SendMessage;
use aieuo\mineflow\flowItem\action\player\SendMessageToOp;
use aieuo\mineflow\flowItem\action\player\SendTitle;
use aieuo\mineflow\flowItem\action\string\EditString;
use aieuo\mineflow\flowItem\action\string\StringLength;
use aieuo\mineflow\flowItem\FlowItemContainer;
use aieuo\mineflow\recipe\Recipe;
use aieuo\mineflow\trigger\block\BlockTrigger;
use aieuo\mineflow\variable\DummyVariable;
use aieuo\mineflow\variable\NumberVariable;
use aieuo\mineflow\variable\StringVariable;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Bread;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use aieuo\mineflow\Main as MineflowMain;

class Main extends PluginBase {

    public function onEnable() {

        // test
        $converter = new MineflowToPHP($this, "mf2php");
        $variables = [
            "aieuo" => new StringVariable("aiueo"),
            "player" => new DummyVariable(DummyVariable::PLAYER),
            "pos" => new DummyVariable(DummyVariable::POSITION),
            "target" => new DummyVariable(DummyVariable::PLAYER),
            "x" => new NumberVariable(100),
            "str" => new StringVariable("100"),
        ];
        $recipe = new Recipe("aieuo");
        $recipe->addTrigger(new BlockTrigger("1,1,2,world"));
        $recipe->addTrigger(new BlockTrigger("1,2,2,world"));
        $recipe->addItem(new SendMessage("target", "hand"), FlowItemContainer::ACTION);
        $recipe->addItem(new SetItemLore("target", "aieuo1;aieuo2;aieuo3;1;233;;aieuo;{player}"), FlowItemContainer::ACTION);
        $recipe->addItem(new GetPlayerByName("target", "player"), FlowItemContainer::ACTION);
        $recipe->addItem(new CreateItemVariable("1", "100", "", "item"), FlowItemContainer::ACTION);
        $recipe->addItem(new AddItem("target", "item"), FlowItemContainer::ACTION);
        $recipe->addItem(new SendMessageToOp("aieuo"), FlowItemContainer::ACTION);
        $recipe->addItem(new SendTitle("target", "title", "subTitle", "-1", "100", "{x}"), FlowItemContainer::ACTION);
        $recipe->addItem(new AddEnchantment("item", "1", "1"), FlowItemContainer::ACTION);
        $recipe->addItem(new EquipArmor("target", "item", "1"), FlowItemContainer::ACTION);
        $recipe->addItem(new GenerateRandomNumber("10", "100", "rand"), FlowItemContainer::ACTION);
        $recipe->addItem(new StringLength("aieuo", "length"), FlowItemContainer::ACTION);
        $recipe->addItem(new EditString("aieuo", EditString::TYPE_JOIN, "b{str}"), FlowItemContainer::ACTION);
        $recipe->addItem(new FourArithmeticOperations("10", FourArithmeticOperations::ADDITION, "{x * (1 + 2}"), FlowItemContainer::ACTION);
        $recipe->addItem(new Calculate("123", Calculate::CALC_SIN), FlowItemContainer::ACTION);
        $recipe->addItem(new Calculate2("12", "23", Calculate2::CALC_ATAN2), FlowItemContainer::ACTION);
        $converter->convert("aieuo\\test", $recipe, $variables);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$command->testPermission($sender)) return true;
        if ($sender instanceof Player) {
            $sender->sendMessage("Please run in console");
            return true;
        }

        if (!isset($args[1])) return false;

        switch ($args[0]) {
            case "mineflow":
            case "mf":
                if (!isset($args[2])) {
                    $sender->sendMessage("Usage: /mfconvert ".$args[0]." ".$args[1]." <recipe>");
                    return true;
                }
                switch ($args[1]) {
                    case "php":
                        $recipe = MineflowMain::getRecipeManager()->get(...MineflowMain::getRecipeManager()->parseName($args[2]));
                        if ($recipe === null) {
                            $sender->sendMessage("The recipe not found");
                            break;
                        }
                        $converter = new MineflowToPHP($this, "mf2php");
                        $converter->convert("aieuo\\test", $recipe);
                        break;
                }
                break;
        }
        return true;
    }
}