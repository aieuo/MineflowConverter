<?php

namespace aieuo\mfconverter\converter;

use aieuo\mfconverter\exception\ConvertException;
use aieuo\mfconverter\template\php\ClassTemplate;
use aieuo\mfconverter\template\php\CodesTemplate;
use aieuo\mfconverter\template\php\MethodTemplate;
use aieuo\mineflow\exception\UndefinedMineflowPropertyException;
use aieuo\mineflow\exception\UndefinedMineflowVariableException;
use aieuo\mineflow\exception\UnsupportedCalculationException;
use aieuo\mineflow\flowItem\action\block\CreateBlockVariable;
use aieuo\mineflow\flowItem\action\command\Command;
use aieuo\mineflow\flowItem\action\command\CommandConsole;
use aieuo\mineflow\flowItem\action\common\DoNothing;
use aieuo\mineflow\flowItem\action\entity\AddDamage;
use aieuo\mineflow\flowItem\action\entity\AddEffect;
use aieuo\mineflow\flowItem\action\entity\GetEntity;
use aieuo\mineflow\flowItem\action\entity\Motion;
use aieuo\mineflow\flowItem\action\entity\SetHealth;
use aieuo\mineflow\flowItem\action\entity\SetImmobile;
use aieuo\mineflow\flowItem\action\entity\SetMaxHealth;
use aieuo\mineflow\flowItem\action\entity\SetNameTag;
use aieuo\mineflow\flowItem\action\entity\SetPitch;
use aieuo\mineflow\flowItem\action\entity\SetScale;
use aieuo\mineflow\flowItem\action\entity\SetYaw;
use aieuo\mineflow\flowItem\action\entity\Teleport;
use aieuo\mineflow\flowItem\action\entity\UnsetImmobile;
use aieuo\mineflow\flowItem\action\event\EventCancel;
use aieuo\mineflow\flowItem\action\form\SendForm;
use aieuo\mineflow\flowItem\action\form\SendInputForm;
use aieuo\mineflow\flowItem\action\form\SendMenuForm;
use aieuo\mineflow\flowItem\action\inventory\AddItem;
use aieuo\mineflow\flowItem\action\inventory\ClearInventory;
use aieuo\mineflow\flowItem\action\inventory\EquipArmor;
use aieuo\mineflow\flowItem\action\inventory\RemoveItem;
use aieuo\mineflow\flowItem\action\inventory\RemoveItemAll;
use aieuo\mineflow\flowItem\action\inventory\SetItem;
use aieuo\mineflow\flowItem\action\inventory\SetItemInHand;
use aieuo\mineflow\flowItem\action\item\AddEnchantment;
use aieuo\mineflow\flowItem\action\item\CreateItemVariable;
use aieuo\mineflow\flowItem\action\item\SetItemCount;
use aieuo\mineflow\flowItem\action\item\SetItemDamage;
use aieuo\mineflow\flowItem\action\item\SetItemLore;
use aieuo\mineflow\flowItem\action\item\SetItemName;
use aieuo\mineflow\flowItem\action\math\Calculate;
use aieuo\mineflow\flowItem\action\math\Calculate2;
use aieuo\mineflow\flowItem\action\math\FourArithmeticOperations;
use aieuo\mineflow\flowItem\action\math\GenerateRandomNumber;
use aieuo\mineflow\flowItem\action\math\GetE;
use aieuo\mineflow\flowItem\action\math\GetPi;
use aieuo\mineflow\flowItem\action\player\AddPermission;
use aieuo\mineflow\flowItem\action\player\AddXpLevel;
use aieuo\mineflow\flowItem\action\player\AddXpProgress;
use aieuo\mineflow\flowItem\action\player\AllowFlight;
use aieuo\mineflow\flowItem\action\player\BroadcastMessage;
use aieuo\mineflow\flowItem\action\player\GetInventoryContents;
use aieuo\mineflow\flowItem\action\player\GetPlayerByName;
use aieuo\mineflow\flowItem\action\player\HideScoreboard;
use aieuo\mineflow\flowItem\action\player\Kick;
use aieuo\mineflow\flowItem\action\player\PlaySound;
use aieuo\mineflow\flowItem\action\player\RemoveBossbar;
use aieuo\mineflow\flowItem\action\player\RemovePermission;
use aieuo\mineflow\flowItem\action\player\SendMessage;
use aieuo\mineflow\flowItem\action\player\SendMessageToOp;
use aieuo\mineflow\flowItem\action\player\SendPopup;
use aieuo\mineflow\flowItem\action\player\SendTip;
use aieuo\mineflow\flowItem\action\player\SendTitle;
use aieuo\mineflow\flowItem\action\player\SetFood;
use aieuo\mineflow\flowItem\action\player\SetGamemode;
use aieuo\mineflow\flowItem\action\player\SetSitting;
use aieuo\mineflow\flowItem\action\player\SetSleeping;
use aieuo\mineflow\flowItem\action\player\ShowBossbar;
use aieuo\mineflow\flowItem\action\scoreboard\CreateScoreboardVariable;
use aieuo\mineflow\flowItem\action\scoreboard\DecrementScoreboardScore;
use aieuo\mineflow\flowItem\action\scoreboard\IncrementScoreboardScore;
use aieuo\mineflow\flowItem\action\scoreboard\RemoveScoreboardScore;
use aieuo\mineflow\flowItem\action\scoreboard\SetScoreboardScore;
use aieuo\mineflow\flowItem\action\scoreboard\SetScoreboardScoreName;
use aieuo\mineflow\flowItem\action\scoreboard\ShowScoreboard;
use aieuo\mineflow\flowItem\action\script\CallRecipe;
use aieuo\mineflow\flowItem\action\script\CreateConfigVariable;
use aieuo\mineflow\flowItem\action\script\ElseAction;
use aieuo\mineflow\flowItem\action\script\ElseifAction;
use aieuo\mineflow\flowItem\action\script\ExecuteRecipe;
use aieuo\mineflow\flowItem\action\script\ExecuteRecipeWithEntity;
use aieuo\mineflow\flowItem\action\script\ExitRecipe;
use aieuo\mineflow\flowItem\action\script\ForeachPosition;
use aieuo\mineflow\flowItem\action\script\IFAction;
use aieuo\mineflow\flowItem\action\script\RepeatAction;
use aieuo\mineflow\flowItem\action\script\SaveConfigFile;
use aieuo\mineflow\flowItem\action\script\SetConfigData;
use aieuo\mineflow\flowItem\action\script\Wait;
use aieuo\mineflow\flowItem\action\script\WhileTaskAction;
use aieuo\mineflow\flowItem\action\string\EditString;
use aieuo\mineflow\flowItem\action\string\StringLength;
use aieuo\mineflow\flowItem\action\variable\AddListVariable;
use aieuo\mineflow\flowItem\action\variable\AddMapVariable;
use aieuo\mineflow\flowItem\action\variable\AddVariable;
use aieuo\mineflow\flowItem\action\variable\CountListVariable;
use aieuo\mineflow\flowItem\action\variable\CreateListVariable;
use aieuo\mineflow\flowItem\action\variable\CreateMapVariable;
use aieuo\mineflow\flowItem\action\variable\DeleteListVariableContent;
use aieuo\mineflow\flowItem\action\variable\DeleteVariable;
use aieuo\mineflow\flowItem\action\variable\GetVariableNested;
use aieuo\mineflow\flowItem\action\variable\JoinListVariableToString;
use aieuo\mineflow\flowItem\action\world\AddParticle;
use aieuo\mineflow\flowItem\action\world\CreatePositionVariable;
use aieuo\mineflow\flowItem\action\world\GetBlock;
use aieuo\mineflow\flowItem\action\world\PlaySoundAt;
use aieuo\mineflow\flowItem\action\world\SetBlock;
use aieuo\mineflow\flowItem\condition\AndScript;
use aieuo\mineflow\flowItem\condition\CanAddItem;
use aieuo\mineflow\flowItem\condition\CheckNothing;
use aieuo\mineflow\flowItem\condition\ComparisonNumber;
use aieuo\mineflow\flowItem\condition\ComparisonString;
use aieuo\mineflow\flowItem\condition\Condition;
use aieuo\mineflow\flowItem\condition\ExistsConfigData;
use aieuo\mineflow\flowItem\condition\ExistsConfigFile;
use aieuo\mineflow\flowItem\condition\ExistsItem;
use aieuo\mineflow\flowItem\condition\ExistsListVariableKey;
use aieuo\mineflow\flowItem\condition\ExistsVariable;
use aieuo\mineflow\flowItem\condition\Gamemode;
use aieuo\mineflow\flowItem\condition\HasPermission;
use aieuo\mineflow\flowItem\condition\InArea;
use aieuo\mineflow\flowItem\condition\InHand;
use aieuo\mineflow\flowItem\condition\IsActiveEntity;
use aieuo\mineflow\flowItem\condition\IsCreature;
use aieuo\mineflow\flowItem\condition\IsFlying;
use aieuo\mineflow\flowItem\condition\IsOp;
use aieuo\mineflow\flowItem\condition\IsPlayer;
use aieuo\mineflow\flowItem\condition\IsPlayerOnline;
use aieuo\mineflow\flowItem\condition\IsPlayerOnlineByName;
use aieuo\mineflow\flowItem\condition\IsSneaking;
use aieuo\mineflow\flowItem\condition\NandScript;
use aieuo\mineflow\flowItem\condition\NorScript;
use aieuo\mineflow\flowItem\condition\NotScript;
use aieuo\mineflow\flowItem\condition\ORScript;
use aieuo\mineflow\flowItem\condition\RandomNumber;
use aieuo\mineflow\flowItem\condition\RemoveItemCondition;
use aieuo\mineflow\flowItem\FlowItem;
use aieuo\mineflow\Main as MineflowMain;
use aieuo\mineflow\recipe\Recipe;
use aieuo\mineflow\trigger\block\BlockTrigger;
use aieuo\mineflow\variable\DefaultVariables;
use aieuo\mineflow\variable\DummyVariable;
use aieuo\mineflow\variable\ListVariable;
use aieuo\mineflow\variable\MapVariable;
use aieuo\mineflow\variable\NumberVariable;
use aieuo\mineflow\variable\ObjectVariable;
use aieuo\mineflow\variable\StringVariable;
use aieuo\mineflow\variable\Variable;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Living;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class MineflowToPHP extends Converter {

    public const PARAM_STRING = "(string)";
    public const PARAM_INT = "(int)";
    public const PARAM_FLOAT = "(float)";

    public function convert(string $namespace, Recipe $recipe, array $variables = null): void {
        $variables = $variables ?? $this->getVariables($recipe);

        $codes = [];
        $uses = [];
        foreach ($recipe->getActions() as $action) {
            try {
                $codes = array_merge($codes, ["", "// ".$action->getName()], $this->convertAction($action, $variables, $uses));
            } catch (ConvertException|UndefinedMineflowVariableException|UndefinedMineflowPropertyException|UnsupportedCalculationException $e) {
                $this->getLogger()->error($e->getMessage());
                return;
            }
        }

        $recipeMethodName = "recipe1";

        $hasEventListener = false;
        $eventListener = $this->createEventListenerClass($namespace);

        $blockTriggers = [];
        foreach ($recipe->getTriggers() as $trigger) {
            if ($trigger instanceof BlockTrigger) {
                $blockTriggers[] = $trigger;
                $hasEventListener = true;
            } else {
                $this->getLogger()->error("Unsupported trigger type: ".$trigger->getType());
                return;
            }
        }
        $args = [];
        if (!empty($blockTriggers)) {
            $args = ['Player $target', 'Block $block'];
            $method = $this->createBlockTriggerMethod($blockTriggers, $recipeMethodName);
            $eventListener->addMethod($method);
        }

        $main = $this->createMainClass($namespace, $hasEventListener);

        $uses = array_unique($uses);
        $codes = new CodesTemplate($codes);
        $method = new MethodTemplate($recipeMethodName, $args, $codes, $uses, true);
        $class = new ClassTemplate($namespace, "Recipes", [], [$method]);

        var_dump($eventListener->format(), $main->format(), $class->format());
    }

    public function createMainClass(string $namespace, bool $hasEventListener): ClassTemplate {
        $onEnable = new MethodTemplate("onEnable", [], new CodesTemplate([]));
        if ($hasEventListener) {
            $onEnable->addCode('Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);', [Server::class]);
        }

        return new ClassTemplate($namespace, "Main", [PluginBase::class], [$onEnable], "PluginBase");
    }

    public function createEventListenerClass(string $namespace): ClassTemplate {
        $uses = [];
        $uses[] = Listener::class;

        return new ClassTemplate($namespace, "Main", $uses, [], null, ["Listener"]);
    }

    /**
     * @param BlockTrigger[] $triggers
     * @param string $target
     * @return MethodTemplate
     */
    public function createBlockTriggerMethod(array $triggers, string $target): MethodTemplate {
        $uses = [];
        $uses[] = PlayerInteractEvent::class;
        $codes = [];
        $codes[] = $this->buildStatement('$event', "getPlayer", [], '$player');
        $codes[] = $this->buildStatement('$event', "getBlock", [], '$block');
        $codes[] = '$position = $block->x.",".$block->y.",".$block->z.",".$block->level->getFolderName();';
        $codes[] = 'switch ($position) {';

        foreach ($triggers as $trigger) {
            $switch = [];
            $case = [];
            $switch[] = 'case "'.$trigger->getKey().'":';
            $case[] = 'Recipes::'.$target.'($player, $block)';
            $case[] = "break;";

            $switch[] = $case;
            $codes[] = $switch;
        }

        $codes[] = '}';
        return new MethodTemplate("onBlockTrigger", ['PlayerInteractEvent $event'], new CodesTemplate($codes), $uses);
    }

    /**
     * @param FlowItem $action
     * @param array $variables
     * @param array $uses
     * @return array|null
     * @throws ConvertException
     */
    private function convertAction(FlowItem $action, array &$variables = [], array &$uses = []): ?array {
        switch (true) {
            case $action instanceof DoNothing:
                return [];
            case $action instanceof EventCancel:
                $uses[] = Cancellable::class;
                return [
                    'if ($event instanceof Cancellable) {', [
                        '$event->setCancelled();'
                    ], '}'];
            case $action instanceof SendMessage:
            case $action instanceof SendTip:
            case $action instanceof SendPopup:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $message = $this->convertContent($action->getMessage(), $variables);
                return [$this->buildStatement($targetPlayer, $action->getId(), [[$message, self::PARAM_STRING]])];
            case $action instanceof BroadcastMessage:
                $uses[] = Server::class;
                $message = $this->convertContent($action->getMessage(), $variables);
                return ['Server::getInstance()->broadcastMessage('.$message.');'];
            case $action instanceof SendMessageToOp:
                $uses[] = Server::class;
                $message = $this->convertContent($action->getMessage(), $variables);
                return [
                    'foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {', [
                        'if ($onlinePlayer->isOp()) {', [
                            '$onlinePlayer->sendMessage('.$message.');'
                        ], '}',
                    ], "}"
                ];
            case $action instanceof SendTitle:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $title = $this->convertContent($action->getTitle(), $variables);
                $subtitle = $this->convertContent($action->getSubTitle(), $variables);
                $times = array_map(function ($time) use($variables) {
                    return $this->convertContent($time, $variables);
                }, $action->getTime());
                return [
                    $this->buildStatement($targetPlayer, "sendTitle", [
                            [$title, self::PARAM_STRING],
                            [$subtitle, self::PARAM_STRING],
                            [$times[0], self::PARAM_INT],
                            [$times[1], self::PARAM_INT],
                            [$times[2], self::PARAM_INT],
                        ]
                    )
                ];
            case $action instanceof SetNameTag:
                $uses[] = Player::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $name = $this->convertContent($action->getNewName(), $variables);
                return [
                    $this->buildStatement($targetEntity, "setNameTag", [[$name, self::PARAM_STRING]]),
                    'if ('.$targetEntity.' instanceof Player) {', [
                        $this->buildStatement($targetEntity, "setDisplayName", [[$name, self::PARAM_STRING]]),
                    ], "}"
                ];
            case $action instanceof GetEntity:
                $id = $this->convertContent($action->getKey(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $uses[] = Server::class;
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::PLAYER);
                return [$this->buildStatement("Server::getInstance()", "findEntity", [[$id, self::PARAM_INT]], '$'.$resultName)];
            case $action instanceof Teleport:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $targetPosition = $this->getTargetVariable($action->getPositionVariableName(), $variables);
                return [$this->buildStatement($targetEntity, "teleport", [$targetPosition])];
            case $action instanceof Motion:
                $uses[] = Vector3::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $positions = array_map(function ($value) use ($variables) {
                    return $this->convertContent($value, $variables);
                }, $action->getPosition());
                $tmpVariable = $this->getNotDuplicatedVariable("amount", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::DUMMY, $tmpVariable);

                return [
                    $this->buildStatement("new", "Vector3", [
                        [$positions[0], self::PARAM_FLOAT],
                        [$positions[1], self::PARAM_FLOAT],
                        [$positions[2], self::PARAM_FLOAT],
                    ], '$'.$tmpVariable, " "),
                    $this->buildStatement($targetEntity, "setMotion", ['$'.$tmpVariable])
                ];
            case $action instanceof SetYaw:
                $uses[] = Player::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $yaw = $this->convertContent($action->getYaw(), $variables);
                return [
                    $this->buildStatement($targetEntity, "setRotation", [[$yaw, self::PARAM_FLOAT], $targetEntity.'->getPitch()']),
                    'if ('.$targetEntity.' instanceof Player) {', [
                        $this->buildStatement($targetEntity, "teleport", [$targetEntity, [$yaw, self::PARAM_FLOAT], $targetEntity.'->getPitch()']),
                    ], "}"
                ];
            case $action instanceof SetPitch:
                $uses[] = Player::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $pitch = $this->convertContent($action->getPitch(), $variables);
                return [
                    $this->buildStatement($targetEntity, "setRotation", [$targetEntity.'->getPitch()', [$pitch, self::PARAM_FLOAT]]),
                    'if ('.$targetEntity.' instanceof Player) {', [
                        $this->buildStatement($targetEntity, "teleport", [$targetEntity, $targetEntity.'->getPitch()', [$pitch, self::PARAM_FLOAT]]),
                    ], "}"
                ];
            case $action instanceof AddDamage:
                $uses[] = EntityDamageEvent::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $cause = $action->getCause();
                $damage = $this->convertContent($action->getDamage(), $variables);
                $tmpVariable = $this->getNotDuplicatedVariable("event", $variables);

                $variables[$tmpVariable] = new DummyVariable(DummyVariable::DUMMY, $tmpVariable);
                return [
                    $this->buildStatement("new", "EntityDamageEvent", [
                        $targetEntity, (string)$cause, [$damage, self::PARAM_FLOAT]
                    ], '$'.$tmpVariable, " "),
                    $this->buildStatement($targetEntity, "addDamage", ['$'.$tmpVariable]),
                ];
            case $action instanceof SetImmobile:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                return [$this->buildStatement($targetEntity, "setImmobile")];
            case $action instanceof UnsetImmobile:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                return [$this->buildStatement($targetEntity, "setImmobile", ["false"])];
            case $action instanceof SetHealth:
            case $action instanceof SetMaxHealth:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $health = $this->convertContent($action->getHealth(), $variables);
                return [$this->buildStatement($targetEntity, $action->getId()), [[$health, self::PARAM_FLOAT]]];
            case $action instanceof SetScale:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $scale = $this->convertContent($action->getScale(), $variables);
                return [$this->buildStatement($targetEntity, $action->getId()), [[$scale, self::PARAM_INT]]];
            case $action instanceof AddEffect:
                $uses[] = Effect::class;
                $uses[] = EffectInstance::class;
                $uses[] = Living::class;
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $effectId = $this->convertContent($action->getEffectId(), $variables);
                $time = $this->convertContent($action->getTime(), $variables);
                $power = $this->convertContent($action->getPower(), $variables);
                $tmpVariable = $this->getNotDuplicatedVariable("effect", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::DUMMY, $tmpVariable);
                return [
                    '$'.$tmpVariable.' = Effect::getEffectByName('.$effectId.') ?? Effect::getEffect('.$effectId.');',
                    'if ('.$tmpVariable.' instanceof Living) {', [
                        $this->buildStatement($targetEntity, "addEffect", [
                            $this->buildStatement("new", "EffectInstance", [
                                $tmpVariable,
                                [$time." * 20", self::PARAM_INT],
                                [$power." - 1", self::PARAM_INT],
                                "false"
                            ], null, " ", false)
                        ])
                    ], "}"
                ];
            case $action instanceof GetPlayerByName:
                $name = $this->convertContent($action->getPlayerName(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::PLAYER, $action->getResultName());
                return [$this->buildStatement("Server::getInstance()", "getPlayer", [$name], '$'.$resultName)];
            case $action instanceof SetSleeping:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $targetPosition = $this->getTargetVariable($action->getPositionVariableName(), $variables);
                return [$this->buildStatement($targetPlayer, "sleepOn", [$targetPosition])];
            case $action instanceof Kick:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $reason = $this->convertContent($action->getReason(), $variables);
                return [$this->buildStatement($targetPlayer, "kick", [$reason])];
            case $action instanceof SetFood:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $food = $this->convertContent($action->getFood(), $variables);
                return [$this->buildStatement($targetPlayer, "setFood", [[$food, self::PARAM_FLOAT]])];
            case $action instanceof SetGamemode:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $gamemode = $this->convertContent($action->getGamemode(), $variables);
                return [$this->buildStatement($targetPlayer, "setGamemode", [[$gamemode, self::PARAM_INT]])];
            case $action instanceof PlaySound:
                $uses[] = PlaySoundPacket::class;
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $sound = $this->convertContent($action->getSound(), $variables);
                $volume = $this->convertContent($action->getVolume(), $variables);
                $pitch = $this->convertContent($action->getPitch(), $variables);
                $tmpVariable = $this->getNotDuplicatedVariable("pk", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::DUMMY, $tmpVariable);
                return [
                    '$'.$tmpVariable.' = new PlaySoundPacket();',
                    '$'.$tmpVariable.'->soundName = '.$sound.';',
                    '$'.$tmpVariable.'->x = '.$targetPlayer.'->x;',
                    '$'.$tmpVariable.'->y = '.$targetPlayer.'->y;',
                    '$'.$tmpVariable.'->z = '.$targetPlayer.'->z;',
                    '$'.$tmpVariable.'->volume = (float)'.$volume.";",
                    '$'.$tmpVariable.'->pitch = (float)'.$pitch.';',
                    $this->buildStatement($targetPlayer, "dataPacket", ['$'.$tmpVariable]),
                ];
            case $action instanceof AddPermission:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $permission = $this->convertContent($action->getPlayerPermission(), $variables);
                return [$this->buildStatement($targetPlayer, "addAttachment", ['Main::getInstance()', $permission, "true"])];
            case $action instanceof RemovePermission:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $permission = $this->convertContent($action->getPlayerPermission(), $variables);
                return [$this->buildStatement($targetPlayer, "addAttachment", ['Main::getInstance()', $permission, "false"])];
            case $action instanceof AddXpProgress:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $xp = $this->convertContent($action->getXp(), $variables);
                return [$this->buildStatement($targetPlayer, "addXp", [[$xp, self::PARAM_INT]])];
            case $action instanceof AddXpLevel:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $xp = $this->convertContent($action->getXp(), $variables);
                return [$this->buildStatement($targetPlayer, "addXpLevels", [[$xp, self::PARAM_INT]])];
            case $action instanceof AllowFlight:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                return [$this->buildStatement($targetPlayer, "setAllowFlight", [$action->isAllow() ? "true" : "false"])];
            case $action instanceof CreateItemVariable:
                $name = $this->convertContent($action->getVariableName(), $variables, true);
                $id = $this->convertContent($action->getItemId(), $variables);
                $count = $this->convertContent($action->getItemCount(), $variables);
                $itemName = $this->convertContent($action->getItemName(), $variables);
                $variables[$action->getVariableName()] = new DummyVariable(DummyVariable::ITEM);
                if (preg_match("/(\d+):?(\d*)/", $id, $matches)) {
                    $uses[] = Item::class;
                    $codes = ['$'.$name.' = Item::get('.$matches[1].', '.(empty($matches[2]) ? 0 : $matches[2]).');'];
                } else {
                    $uses[] = ItemFactory::class;
                    $codes = ['$'.$name.' = ItemFactory::fromString('.$id.');'];
                }
                $codes[] = $this->buildStatement('$'.$name, "setCount", [[empty($count) ? ('$'.$name.'->getMaxStackSize()') : $count, self::PARAM_INT]]);
                if (!empty($itemName) and $itemName !== '""') {
                    $codes[] = $this->buildStatement('$'.$name, "setCustomName", [$itemName]);
                }
                return $codes;
            case $action instanceof AddItem:
            case $action instanceof SetItemInHand:
            case $action instanceof RemoveItem:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                return [$this->buildStatement($targetPlayer, "getInventory()->".$action->getId(), [$targetItem])];
            case $action instanceof RemoveItemAll:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                return [$this->buildStatement($targetPlayer, "getInventory()->remove", [$targetItem])];
            case $action instanceof SetItemDamage:
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $damage = $this->convertContent($action->getDamage(), $variables);
                return [$this->buildStatement($targetItem, "setDamage", [[$damage, self::PARAM_INT]])];
            case $action instanceof SetItemCount:
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $count = $this->convertContent($action->getCount(), $variables);
                return [$this->buildStatement($targetItem, "setCount", [[$count, self::PARAM_INT]])];
            case $action instanceof SetItemName:
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $name = $this->convertContent($action->getItemName(), $variables);
                return [$this->buildStatement($targetItem, "setCustomName", [[$name, self::PARAM_STRING]])];
            case $action instanceof SetItemLore:
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $lore = "[".implode(", ", array_map(function (string $lore) use ($variables) {
                    return $this->convertContent($lore, $variables);
                }, $action->getLore()))."]";
                return [$this->buildStatement($targetItem, "setLore", [$lore])];
            case $action instanceof AddEnchantment:
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $id = $this->convertContent($action->getEnchantId(), $variables);
                $level = $this->convertContent($action->getEnchantLevel(), $variables);
                $tmpVariable = $this->getNotDuplicatedVariable("enchant", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::DUMMY, $tmpVariable);
                $uses[] = Enchantment::class;
                $uses[] = EnchantmentInstance::class;
                if (preg_match("/(\d+)/", $id, $matches)) {
                    $codes = ["$".$tmpVariable." = Enchantment::getEnchantment(".$matches[1].");"];
                } else {
                    $codes = ["$".$tmpVariable." = Enchantment::getEnchantment((int)(".$id.")) ?? Enchantment::getEnchantmentByName(".$id.");"];
                }
                $codes[] = $this->buildStatement($targetItem, "addEnchantment", [
                    $this->buildStatement("new", "EnchantmentInstance", ["$".$tmpVariable, [$level, self::PARAM_INT]], null, " ", false)
                ]);
                return $codes;
            case $action instanceof EquipArmor:
                $targetEntity = $this->getTargetVariable($action->getEntityVariableName(), $variables);
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $index = $this->convertContent($action->getIndex(), $variables);
                $uses[] = Living::class;
                return ["if (".$targetEntity." instanceof Living) {", [
                    $this->buildStatement($targetEntity, "getArmorInventory", [[$index, self::PARAM_INT], $targetItem])
                ], "}"];
            case $action instanceof SetItem:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($action->getItemVariableName(), $variables);
                $index = $this->convertContent($action->getIndex(), $variables);
                return [$this->buildStatement($targetPlayer, "getInventory()->setItem", [[$index, self::PARAM_INT], $targetItem])];
            case $action instanceof ClearInventory:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                return [$this->buildStatement($targetPlayer, "getInventory()->clearAll", ["true"])];
            case $action instanceof GetInventoryContents:
                $targetPlayer = $this->getTargetVariable($action->getPlayerVariableName(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $variables[$action->getResultName()] = new ListVariable([], $action->getResultName());
                return [$this->buildStatement($targetPlayer, "getInventory()->getContents()", [], "$".$resultName)];
            case $action instanceof GetPi:
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                return ["$".$resultName." = M_PI;"];
            case $action instanceof GetE:
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                return ["$".$resultName." = M_E;"];
            case $action instanceof GenerateRandomNumber:
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $min = $this->convertContent($action->getMin(), $variables);
                $max = $this->convertContent($action->getMax(), $variables);
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                return [$this->buildStatement("", "mt_rand", [[$min, self::PARAM_INT], [$max, self::PARAM_INT]], "$".$resultName, "")];
            case $action instanceof StringLength:
                $value = $this->convertContent($action->getValue(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                return [$this->buildStatement("", "mb_strlen", [$value], "$".$resultName, "")];
            case $action instanceof EditString:
                $value1 = $this->convertContent($action->getValue1(), $variables);
                $value2 = $this->convertContent($action->getValue2(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $operator = $action->getOperator();
                $codes = [];
                switch ($operator) {
                    case EditString::TYPE_JOIN:
                        $codes[] = "$".$resultName." = ".$value1.".".$value2.";";
                        $variables[$action->getResultName()] = new DummyVariable(DummyVariable::STRING);
                        break;
                    case EditString::TYPE_DELETE:
                        $codes[] = $this->buildStatement("", "str_replace", [$value2, '""', $value1], "$".$resultName, "");
                        $variables[$action->getResultName()] = new DummyVariable(DummyVariable::STRING);
                        break;
                    case EditString::TYPE_REPEAT:
                        $codes[] = $this->buildStatement("", "str_repeat", [$value1, [$value2, self::PARAM_INT]], "$".$resultName, "");
                        $variables[$action->getResultName()] = new DummyVariable(DummyVariable::STRING);
                        break;
                    case EditString::TYPE_SPLIT:
                        $codes[] = $this->buildStatement("", "explode", [$value2, $value1], "$".$resultName, "");
                        $variables[$action->getResultName()] = new DummyVariable(DummyVariable::LIST, DummyVariable::STRING);
                        break;
                }
                return $codes;
            case $action instanceof FourArithmeticOperations:
                $value1 = $this->convertContent($action->getValue1(), $variables);
                $value2 = $this->convertContent($action->getValue2(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $operator = $action->getOperator();
                $codes = [];
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                $value1 = preg_match("/\"(-?\d+.?\d*)\"/", $value1, $matches) ? $matches[1] : ("(float)".$value1);
                $value2 = preg_match("/\"(-?\d+.?\d*)\"/", $value2, $matches) ? $matches[1] : ("(float)".$value2);
                switch ($operator) {
                    case FourArithmeticOperations::ADDITION:
                        $codes[] = "$".$resultName." = ".$value1." + ".$value2.";";
                        break;
                    case FourArithmeticOperations::SUBTRACTION:
                        $codes[] = "$".$resultName." = ".$value1." - ".$value2.";";
                        break;
                    case FourArithmeticOperations::MULTIPLICATION:
                        $codes[] = "$".$resultName." = ".$value1." * ".$value2.";";
                        break;
                    case FourArithmeticOperations::DIVISION:
                        $codes[] = "$".$resultName." = ".$value1." / ".$value2.";";
                        break;
                    case FourArithmeticOperations::MODULO:
                        $codes[] = "$".$resultName." = ".$value1." % ".$value2.";";
                        break;
                }
                return $codes;
            case $action instanceof Calculate:
                $value = $this->convertContent($action->getValue(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $operator = $action->getOperator();
                $codes = [];
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                switch ($operator) {
                    case Calculate::SQUARE:
                        $value = preg_match("/\"(-?\d+.?\d*)\"/", $value, $matches) ? $matches[1] : ("(float)".$value);
                        $codes[] = "$".$resultName." = ".$value." * ".$value.";";
                        break;
                    case Calculate::SQUARE_ROOT:
                        $codes[] = $this->buildStatement("", "sqrt", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::FACTORIAL:
                        $i = $this->getNotDuplicatedVariable("i", $variables);
                        $variables[$i] = new DummyVariable(DummyVariable::NUMBER);
                        $codes[] = "$".$resultName." = 1;";
                        $codes[] = "for ($".$i."=abs(".$value."); $".$i.">1; $".$i."--) {";
                        $codes[][] = "$".$resultName." *= $".$i.";";
                        $codes[] = ";";
                        break;
                    case Calculate::CALC_ABS:
                        $codes[] = $this->buildStatement("", "abs", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_LOG:
                        $codes[] = $this->buildStatement("", "log10", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_SIN:
                        $codes[] = $this->buildStatement("", "sin", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_COS:
                        $codes[] = $this->buildStatement("", "cos(", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_TAN:
                        $codes[] = $this->buildStatement("", "tan(", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_ASIN:
                        $codes[] = $this->buildStatement("", "asin", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_ACOS:
                        $codes[] = $this->buildStatement("", "acos", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_ATAN:
                        $codes[] = $this->buildStatement("", "atan", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_DEG2RAD:
                        $codes[] = $this->buildStatement("", "deg2rad", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_RAD2DEG:
                        $codes[] = $this->buildStatement("", "rad2deg", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_FLOOR:
                        $codes[] = $this->buildStatement("", "floor", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_ROUND:
                        $codes[] = $this->buildStatement("", "round", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate::CALC_CEIL:
                        $codes[] = $this->buildStatement("", "ceil", [[$value, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                }
                return $codes;
            case $action instanceof Calculate2:
                $value1 = $this->convertContent($action->getValue1(), $variables);
                $value2 = $this->convertContent($action->getValue2(), $variables);
                $resultName = $this->convertContent($action->getResultName(), $variables, true);
                $operator = $action->getOperator();
                $codes = [];
                $variables[$action->getResultName()] = new DummyVariable(DummyVariable::NUMBER);
                switch ($operator) {
                    case Calculate2::CALC_MIN:
                        $codes[] = $this->buildStatement("", "min", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_MAX:
                        $codes[] = $this->buildStatement("", "max", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_POW:
                        $codes[] = $this->buildStatement("", "pow", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_LOG:
                        $codes[] = $this->buildStatement("", "log", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_HYPOT:
                        $codes[] = $this->buildStatement("", "hypot", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_ATAN2:
                        $codes[] = $this->buildStatement("", "atan2", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_FLOAT]], "$".$resultName, "");
                        break;
                    case Calculate2::CALC_ROUND:
                        $codes[] = $this->buildStatement("", "round", [[$value1, self::PARAM_FLOAT], [$value2, self::PARAM_INT]], "$".$resultName, "");
                        break;
                }
                return $codes;
            case $action instanceof CreatePositionVariable:
                $resultName = $this->convertContent($action->getVariableName(), $variables, true);
                $x = $this->convertContent($action->getX(), $variables);
                $y = $this->convertContent($action->getY(), $variables);
                $z = $this->convertContent($action->getZ(), $variables);
                $levelName = $this->convertContent($action->getLevel(), $variables);

                $uses[] = Position::class;
                $variables[$action->getVariableName()] = new DummyVariable(DummyVariable::POSITION);
                return [$this->buildStatement("new", "Position", [
                    [$x, self::PARAM_FLOAT],
                    [$y, self::PARAM_FLOAT],
                    [$z, self::PARAM_FLOAT],
                    [$levelName, self::PARAM_STRING],
                ], '$'.$resultName, " ")];
            case $action instanceof CreateBlockVariable:
                $resultName = $this->convertContent($action->getVariableName(), $variables, true);
                $id = $this->convertContent($action->getBlockId(), $variables);

                $codes = [];
                if (preg_match("/(\d+):?(\d*)/", $id, $matches)) {
                    $uses[] = BlockFactory::class;
                    $codes[] = '$'.$resultName.' = BlockFactory::get('.$matches[1].', '.(empty($matches[2]) ? 0 : $matches[2]).');';
                } else {
                    $tmpVariable = $this->getNotDuplicatedVariable("item", $variables);
                    $variables[$tmpVariable] = new DummyVariable(DummyVariable::ITEM);

                    $uses[] = ItemFactory::class;
                    $codes[] = '$'.$tmpVariable.' = ItemFactory::fromString('.$id.');';
                    $codes[] = $this->buildStatement('$'.$resultName, "getBlock", [], '$'.$tmpVariable);
                }

                $variables[$action->getVariableName()] = new DummyVariable(DummyVariable::BLOCK);
                return $codes;
            case $action instanceof ForeachPosition:
                $pos1 = $this->getTargetVariable($action->getPositionVariableName("pos1"), $variables);
                $pos2 = $this->getTargetVariable($action->getPositionVariableName("pos2"), $variables);

                $variables[$sx = $this->getNotDuplicatedVariable("sx", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$sy = $this->getNotDuplicatedVariable("sy", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$sz = $this->getNotDuplicatedVariable("sz", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$ex = $this->getNotDuplicatedVariable("ex", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$ey = $this->getNotDuplicatedVariable("ey", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$ez = $this->getNotDuplicatedVariable("ez", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$x = $this->getNotDuplicatedVariable("x", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$y = $this->getNotDuplicatedVariable("y", $variables)] = new DummyVariable(DummyVariable::NUMBER);
                $variables[$z = $this->getNotDuplicatedVariable("z", $variables)] = new DummyVariable(DummyVariable::NUMBER);

                $tmpVariable = $this->getNotDuplicatedVariable("pos", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::POSITION);
                $uses[] = Position::class;

                $insides = [$this->buildStatement("new", "Position", ["$".$x, "$".$y, "$".$z, $pos1."->getLevel()"], "$".$tmpVariable, " ")];
                foreach ($action->getActions() as $item) {
                    foreach ($this->convertAction($item, $variables, $uses) as $line) {
                        $insides[] = $line;
                    }
                }

                $codes = [
                    $this->buildStatement("", "min", [$pos1."->x", $pos2."->x"], "$".$sx, ""),
                    $this->buildStatement("", "min", [$pos1."->y", $pos2."->y"], "$".$sy, ""),
                    $this->buildStatement("", "min", [$pos1."->z", $pos2."->z"], "$".$sz, ""),
                    $this->buildStatement("", "max", [$pos1."->x", $pos2."->x"], "$".$ex, ""),
                    $this->buildStatement("", "max", [$pos1."->y", $pos2."->y"], "$".$ey, ""),
                    $this->buildStatement("", "max", [$pos1."->z", $pos2."->z"], "$".$ez, ""),
                    'for ($'.$x.' = $'.$sx.'; $'.$x.' <= $'.$ex.'; $'.$x.'++) {', [
                        'for ($'.$y.' = $'.$sy.'; $'.$y.' <= $'.$ey.'; $'.$y.'++) {', [
                            'for ($'.$z.' = $'.$sz.'; $'.$z.' <= $'.$ez.'; $'.$z.'++) {',
                                $insides,
                            "}",
                        ], "}",
                    ], "}",
                ];
                return $codes;
            case $action instanceof SetBlock:
                $position = $this->getTargetVariable($action->getPositionVariableName(), $variables);
                $block = $this->getTargetVariable($action->getBlockVariableName(), $variables);
                return [
                    $this->buildStatement($position."->level", "setBlock", [$position, $block])
                ];
            case $action instanceof AddParticle:
                $position = $this->getTargetVariable($action->getPositionVariableName(), $variables);
                $particleName = $this->convertContent($action->getParticle(), $variables);
                $amount = $this->convertContent($action->getAmount(), $variables);

                $uses[] = SpawnParticleEffectPacket::class;
                $uses[] = Server::class;

                $tmpVariable = $this->getNotDuplicatedVariable("pk", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::UNKNOWN, $tmpVariable);
                $particleCode = [
                    '$'.$tmpVariable.' = new SpawnParticleEffectPacket();',
                    '$'.$tmpVariable.'->position = '.$position.';',
                    '$'.$tmpVariable.'->particleName = '.$particleName.';',
                    $this->buildStatement('Server::getInstance()', "broadcastPacket", [
                        $this->buildStatement($position."->level", "getPlayers", [], null, "->", false),
                        '$'.$tmpVariable
                    ]),
                ];
                if ($amount === "1") {
                    return $particleCode;
                }

                $tmpVariable = $this->getNotDuplicatedVariable("i", $variables);
                $variables[$tmpVariable] = new DummyVariable(DummyVariable::UNKNOWN, $tmpVariable);
                return [
                    'for ($'.$tmpVariable.' = 0; $'.$tmpVariable.' < '.$this->castValue($amount, self::PARAM_INT).'; $'.$tmpVariable.'++) {',
                    $particleCode,
                    '}'
                ];
            case $action instanceof IFAction:
            case $action instanceof ElseifAction:
            case $action instanceof ElseAction:
            case $action instanceof RepeatAction:
            case $action instanceof WhileTaskAction:
            case $action instanceof Wait:
            case $action instanceof CallRecipe:
            case $action instanceof ExecuteRecipe:
            case $action instanceof ExecuteRecipeWithEntity:
            case $action instanceof CreateConfigVariable:
            case $action instanceof SetConfigData:
            case $action instanceof SaveConfigFile:
            case $action instanceof ExitRecipe:
            case $action instanceof AddVariable:
            case $action instanceof DeleteVariable:
            case $action instanceof CreateListVariable:
            case $action instanceof AddListVariable:
            case $action instanceof CreateMapVariable:
            case $action instanceof AddMapVariable:
            case $action instanceof DeleteListVariableContent:
            case $action instanceof GetVariableNested:
            case $action instanceof CountListVariable:
            case $action instanceof JoinListVariableToString:
            case $action instanceof SendForm:
            case $action instanceof SendInputForm:
            case $action instanceof SendMenuForm:
            case $action instanceof Command:
            case $action instanceof CommandConsole:
            case $action instanceof GetBlock:
            case $action instanceof PlaySoundAt:
            case $action instanceof CreateScoreboardVariable:
            case $action instanceof SetScoreboardScore:
            case $action instanceof SetScoreboardScoreName:
            case $action instanceof IncrementScoreboardScore:
            case $action instanceof DecrementScoreboardScore:
            case $action instanceof RemoveScoreboardScore:
            case $action instanceof SetSitting:
            case $action instanceof ShowBossbar:
            case $action instanceof RemoveBossbar:
            case $action instanceof ShowScoreboard:
            case $action instanceof HideScoreboard:
            default:
                throw new ConvertException("Unsupported action: ".$action->getName());
        }
    }

    /**
     * @param FlowItem $condition
     * @param array $variables
     * @param array $uses
     * @return array|null
     * @throws ConvertException
     */
    private function convertCondition(FlowItem $condition, array &$variables = [], array &$uses = []): ?array {
        $pushes = [];
        $codes = [];
        $appends = [];
        switch (true) {
            case $condition instanceof CheckNothing:
                $codes = ["true"];
                break;
            case $condition instanceof IsOp:
            case $condition instanceof IsSneaking:
            case $condition instanceof IsFlying:
                $targetPlayer = $this->getTargetVariable($condition->getPlayerVariableName(), $variables);
                $codes = [$this->buildStatement($targetPlayer, $condition->getId(), [], null, null, false)];
                break;
            case $condition instanceof RandomNumber:
                $min = $this->convertContent($condition->getMin(), $variables);
                $max = $this->convertContent($condition->getMax(), $variables);
                $value = $this->convertContent($condition->getValue(), $variables);
                $codes = [$this->buildStatement("", "mt_rand", [[$min, self::PARAM_INT], [$max, self::PARAM_INT]], null, "", false)." === (int)".$value];
                break;
            case $condition instanceof InHand:
                $targetPlayer = $this->getTargetVariable($condition->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($condition->getItemVariableName(), $variables);
                $hand = $this->getNotDuplicatedVariable("hand", $variables);
                $variables[$hand] = new DummyVariable(DummyVariable::ITEM, $hand);
                $hand = "$".$hand;
                $pushes = [$this->buildStatement($targetPlayer, "getInventory()->getItemInHand()", [], $hand)];
                $codes = [
                    $targetItem."->getId() === ".$hand."->getId()",
                    $targetItem."->getDamage() === ".$hand."->getDamage()",
                    $targetItem."->getCount() === ".$hand."->getCount()",
                    "(!".$targetItem."->hasCustomName() or ".$targetItem."->getName() === ".$hand."->getName())",
                    "(empty(".$targetItem."->getLore()) or ".$targetItem."->getLore() === ".$hand."->getLore())",
                    "(empty(".$targetItem."->getEnchantments()) or ".$targetItem."->getEnchantments() === ".$hand."->getEnchantments())",
                ];
                break;
            case $condition instanceof ExistsItem:
                $targetPlayer = $this->getTargetVariable($condition->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($condition->getItemVariableName(), $variables);
                $codes = [$this->buildStatement($targetPlayer, "getInventory()->contains", [$targetItem], null, null, false)];
                break;
            case $condition instanceof CanAddItem:
                $targetPlayer = $this->getTargetVariable($condition->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($condition->getItemVariableName(), $variables);
                $codes = [$this->buildStatement($targetPlayer, "getInventory()->canAddItem", [$targetItem], null, null, false)];
                break;
            case $condition instanceof RemoveItemCondition:
                $targetPlayer = $this->getTargetVariable($condition->getPlayerVariableName(), $variables);
                $targetItem = $this->getTargetVariable($condition->getItemVariableName(), $variables);
                $codes = [$this->buildStatement($targetPlayer, "getInventory()->contains", [$targetItem], null, null, false)];
                $appends = [$this->buildStatement($targetPlayer, "getInventory()->removeItem", [$targetItem])];
                break;
            case $condition instanceof ComparisonNumber:
            case $condition instanceof ComparisonString:
            case $condition instanceof AndScript:
            case $condition instanceof ORScript:
            case $condition instanceof NotScript:
            case $condition instanceof NorScript:
            case $condition instanceof NandScript:
            case $condition instanceof ExistsConfigFile:
            case $condition instanceof ExistsConfigData:
            case $condition instanceof IsActiveEntity:
            case $condition instanceof IsPlayer:
            case $condition instanceof IsCreature:
            case $condition instanceof InArea:
            case $condition instanceof Gamemode:
            case $condition instanceof HasPermission:
            case $condition instanceof IsPlayerOnline:
            case $condition instanceof IsPlayerOnlineByName:
            case $condition instanceof ExistsVariable:
            case $condition instanceof ExistsListVariableKey:
            default:
                throw new ConvertException("Unsupported condition: ".$condition->getName());
        }
        return [$pushes, $codes, $appends];
    }

    /**
     * @param string $content
     * @param array $variables
     * @param bool $isVariable
     * @return string
     * @throws ConvertException
     */
    public function convertContent(string $content, array $variables, bool $isVariable = false): string {
        $helper = MineflowMain::getVariableHelper();
        $founds = $helper->findVariables($content);

        foreach ($founds as $found) {
            if (strpos($found, "{") !== false and strpos($found, "}") !== false) {
                throw new ConvertException("Nesting variables are not supported. {".$found."}");
            }

            $tokens = $helper->lexer($found);
            $ast = $helper->parse($tokens);
            if ($ast instanceof Variable) {
                $content = str_replace("{".$found."}", (string)$ast, $content);
                continue;
            }
            if (is_string($ast)) {
                $variable = $helper->mustGetVariableNested($ast, $variables);

                $names = explode(".", $ast);
                $name = array_shift($names);

                $variableStr = $this->convertVariable($variable, $names, '$'.$name);
                $content = str_replace("{".$found."}", '".'.$variableStr.'."', $content);
                continue;
            }

            $content = str_replace("{".$found."}", '".'.$this->buildVariableString($ast, $variables).'."', $content);
        }
        if (strpos($content, '".') === 0) {
            $content = substr($content, 2);
        } elseif (!$isVariable) {
            $content = '"'.$content;
        }
        if (substr($content, -2) === '."') {
            $content = substr($content, 0, -2);
        } elseif (!$isVariable) {
            $content .= '"';
        }
        if ($isVariable and strpos($content, '"') !== false) {
            $content = "{".$content."}";
        }
        return str_replace('."".', '.', $content);
    }

    public function buildVariableString($ast, array $variables = []): string {
        $left = is_array($ast["left"]) ? $this->buildVariableString($ast["left"], $variables) : $ast["left"];
        $right = is_array($ast["right"]) ? $this->buildVariableString($ast["right"], $variables) : $ast["right"];
        $op = $ast["op"];

        if ($op === "()") throw new UnsupportedCalculationException();

        $helper = MineflowMain::getVariableHelper();
        if (is_string($left) and $left[0] !== "(") {
            $variable = $helper->mustGetVariableNested($left, $variables); // TODO: check number variable
            $names = explode(".", $left);
            $name = array_shift($names);
            $left = $this->convertVariable($variable, $names, '$'.$name);
        }
        if (is_string($right) and $right[0] !== "(") {
            $variable = $helper->mustGetVariableNested($right, $variables); // TODO: check number variable
            $names = explode(".", $right);
            $name = array_shift($names);
            $right = $this->convertVariable($variable, $names, '$'.$name);
        }

        return "(".$left." ".$op." ".$right.")";
    }

    private function convertVariable(Variable $variable, array $properties, string $variableStr = ""): string {
        foreach ($properties as $i => $index) {
            if ($variable instanceof StringVariable or $variable instanceof NumberVariable) break;

            if ($variable instanceof MapVariable) {
                $variableStr .= '["'.$index.'"]';
                $variable = $variable->getValueFromIndex($index);
            } elseif ($variable instanceof ListVariable) {
                $variableStr .= '['.$index.']';
                $variable = $variable->getValueFromIndex($index);
            } elseif ($variable instanceof DummyVariable) {
                switch ($variable->getValueType()) {
                    case DummyVariable::STRING:
                    case DummyVariable::NUMBER:
                    case DummyVariable::BOOLEAN:
                        break 2;
                    case DummyVariable::LIST:
                        $variableStr .= '['.$index.']';
                        $variable = new DummyVariable($variable->getDescription());
                        continue 2;
                    case DummyVariable::MAP:
                        $variableStr .= '["'.$index.'"]';
                        $variable = new DummyVariable($variable->getDescription());
                        continue 2;
                    case DummyVariable::EVENT:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getEventName()";
                                break;
                            case "isCanceled":
                                $variableStr .= '->isCancelled()';
                                break;
                        }
                        break;
                    case DummyVariable::ITEM:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getName()";
                                break;
                            case "id":
                                $variableStr .= "->getId()";
                                break;
                            case "damage":
                                $variableStr .= "->getDamage()";
                                break;
                            case "count":
                                $variableStr .= "->getCount()";
                                break;
                            case "lore":
                                $variableStr .= "->getLore()";
                                $variable = new ListVariable([]);
                                continue 3;
                        }
                        break;
                    case DummyVariable::WORLD:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getName()";
                                break;
                            case "folderName":
                                $variableStr .= "->getFolderName()";
                                break;
                            case "id":
                                $variableStr .= "->getId()";
                                break;
                        }
                        break;
                    /** @noinspection PhpMissingBreakStatementInspection */
                    case DummyVariable::PLAYER:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getName()";
                                break;
                        }
                    /** @noinspection PhpMissingBreakStatementInspection */
                    case DummyVariable::HUMAN:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getName()";
                                break;
                            case "hand":
                                $variableStr .= "->getInventory()->getItemInHand()";
                                $variable = new DummyVariable(DummyVariable::ITEM, "hand");
                                continue 3;
                            case "food":
                                $variableStr .= "->getFood()";
                                break;
                        }
                    /** @noinspection PhpMissingBreakStatementInspection */
                    case DummyVariable::ENTITY:
                        switch ($index) {
                            case "id":
                                $variableStr .= "->getId()";
                                break;
                            case "nameTag":
                                $variableStr .= "->getNameTag()";
                                break;
                            case "health":
                                $variableStr .= "->getHealth()";
                                break;
                            case "maxHealth":
                                $variableStr .= "->getMaxHealth()";
                                break;
                            case "yaw":
                                $variableStr .= "->getYaw()";
                                break;
                            case "pitch":
                                $variableStr .= "->getPitch()";
                                break;
                        }
                    /** @noinspection PhpMissingBreakStatementInspection */
                    case DummyVariable::POSITION:
                        switch ($index) {
                            case "position":
                                $variableStr .= "->asPosition()";
                                $variable = new DummyVariable(DummyVariable::POSITION);
                                continue 3;
                            case "world":
                                $variableStr .= "->getLevel()";
                                $variable = new DummyVariable(DummyVariable::WORLD);
                                continue 3;
                        }
                    case DummyVariable::VECTOR3:
                        switch ($index) {
                            case "x":
                                $variableStr .= "->getX()";
                                break;
                            case "y":
                                $variableStr .= "->getY()";
                                break;
                            case "z":
                                $variableStr .= "->getZ()";
                                break;
                            case "xyz":
                                $variableStr = $variableStr.'->x.",".'.$variableStr.'->y.",".'.$variableStr."->z";
                                break;
                        }
                        break;
                    case DummyVariable::BLOCK:
                        switch ($index) {
                            case "name":
                                $variableStr .= "->getName()";
                                break;
                            case "id":
                                $variableStr .= "->getId()";
                                break;
                            case "damage":
                                $variableStr .= "->getDamage()";
                                break;
                            case "x":
                                $variableStr .= "->getX()";
                                break;
                            case "y":
                                $variableStr .= "->getY()";
                                break;
                            case "z":
                                $variableStr .= "->getZ()";
                                break;
                            case "xyz":
                                $variableStr = $variableStr.'->x.",".'.$variableStr.'->y.",".'.$variableStr."->z";
                                break;
                            case "position":
                                $variableStr .= "->asPosition()";
                                $variable = new DummyVariable(DummyVariable::POSITION);
                                continue 3;
                            case "world":
                                $variableStr .= "->getLevel()";
                                $variable = new DummyVariable(DummyVariable::WORLD);
                                continue 3;
                        }
                        break;
                    case DummyVariable::CONFIG:
                        $key = implode(".", array_slice($properties, $i));
                        $variableStr .= '->getNexted("'.$key.'")';
                        break;
                    case DummyVariable::SCOREBOARD:
                        break;
                }
                break;
            }
        }
        return $variableStr;
    }

    private function getTargetVariable(string $target, array $variables): string {
        $names = explode(".", $target);
        $name = array_shift($names);
        if (empty($names)) return '$'.$target;

        return $this->convertVariable($variables[$name], $names, '$'.$name);
    }

    /**
     * @param string $class
     * @param string $method
     * @param string[]|array[] $args
     * @phpstan-param (string|array)[] $args
     * @param string|null $assign
     * @param string|null $separator
     * @param bool $semicolon
     * @return string
     */
    private function buildStatement(string $class, string $method, array $args = [], string $assign = null, string $separator = null, bool $semicolon = true): string {
        $separator = $separator ?? "->";
        return ($assign === null ? "" : ($assign." = ")).
            $class.$separator.$method."(".implode(", ", array_map(function ($arg) {
                if (!is_array($arg)) return $arg;
                return $this->castValue($arg[0], $arg[1]);
            }, $args)).")".($semicolon ? ";" : "");
    }

    private function castValue(string $value, string $type): string {
        if ($type === self::PARAM_STRING) return $value;

        if (preg_match("/\"(-?\d+.?\d*)\"/", $value, $matches)) $value = $matches[1];
        return is_numeric($value) ? $value : ($type.(strpos($value, ".") !== false ? ("(".$value.")") : $value));
    }

    private function getNotDuplicatedVariable(string $name, array $variables): string {
        if (!isset($variables[$name])) return $name;

        $cnt = 0;
        $newName = $name.$cnt;
        while (isset($variables[$newName])) {
            $cnt ++;
            $newName = $name.$cnt;
        }
        return $newName;
    }

    private function getVariables(Recipe $recipe): array {
        $variables = array_merge(DefaultVariables::getServerVariables(), [
            "target" => new DummyVariable(DummyVariable::PLAYER),
            "event" => new DummyVariable(DummyVariable::EVENT),
        ]);
        $triggers = $recipe->getTriggers();
        foreach ($triggers as $trigger) {
            foreach ($trigger->getVariablesDummy() as $name => $variable) {
                $variables[$name] = $variable;
            }
        }
        return $variables;
    }
}