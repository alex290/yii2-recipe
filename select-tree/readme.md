## Дерево категории в SELECT ##

Для начала мы создаем функцию которая преобразует модель в многомерный массив



	public function getTree() {
        $tree = [];
        $catstree = Category::find()->indexBy('id')->asArray()->all();
        foreach ($catstree as $id => &$node) {
            if (!$node['parent_id'])
                $tree[$id] = &$node;
            else
                $catstree[$node['parent_id']]['childs'][$node['id']] = &$node;
        }
        ArrayHelper::multisort($tree, 'weight');
        $treeOne = ArrayHelper::map($this->Tree($tree), 'id', 'name');

        return $treeOne;
    }

и вставим рекурсивную функцию  которая к дочерним элементам подпишет тире

    public function getTree() {
        $tree = [];
        $catstree = Category::find()->indexBy('id')->asArray()->all();
        foreach ($catstree as $id => &$node) {
            if (!$node['parent_id'])
                $tree[$id] = &$node;
            else
                $catstree[$node['parent_id']]['childs'][$node['id']] = &$node;
        }
        ArrayHelper::multisort($tree, 'weight'); // Сортируем массив по полю weight. Если это необходимо
        $treeOne = ArrayHelper::map($this->Tree($tree), 'id', 'name'); //Вызываем функцию Tree и преобразуем его для select

        return $treeOne;
    }
